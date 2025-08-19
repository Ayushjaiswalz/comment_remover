<?php
function isCodeLike($comment) {
    // Simple heuristic: if comment contains PHP keywords, variables, code-like symbols, or HTML tags
    $patterns = [
        '/\bfunction\b\s*\w*\s*\(/',
        '/\bclass\b\s+\w+/',
        '/\bif\s*\(/',
        '/\bfor\s*\(/',
        '/\bwhile\s*\(/',
        '/\bswitch\s*\(/',
        '/\$\w+/',          // variables like $var
        '/;/',              // statement end
        '/\=/',             // assignment
        '/\{|\}/',          // braces
        '/<\?php/',         // embedded PHP
        '/<[^>]+>/',        // any HTML tag (e.g., <table>, <div>, etc.)
    ];

    foreach ($patterns as $pattern) {
        if (preg_match($pattern, $comment)) {
            return true; // Looks like code
        }
    }
    return false; // Normal text comment
}

// More conservative detection for HTML comments (inner content only)
function isCodeLikeHtmlComment($text) {
	if (preg_match('/<\?php/i', $text)) return true;
	if (preg_match('/<\/?\w+[^>]*>/', $text)) return true; // HTML tags inside comment
	return false;
}

function removeComments($code, &$allComments) {
	$tokens = token_get_all($code);
	$output = '';
	$newline = (strpos($code, "\r\n") !== false) ? "\r\n" : "\n";

	$tokenCount = count($tokens);
	for ($i = 0; $i < $tokenCount; $i++) {
		$token = $tokens[$i];

		if (is_array($token)) {
			[$id, $text] = $token;

			if ($id === T_COMMENT || $id === T_DOC_COMMENT) {
				$allComments[] = $text; // store found comment

				if (isCodeLike($text)) {
					// Remove trailing spaces before the removed comment
					$output = rtrim($output, " \t");

					$commentHasNewline = (strpos($text, "\n") !== false || strpos($text, "\r") !== false);
					$followingHasNewline = false;

					// Consume immediate following whitespace tokens to avoid gaps
					$j = $i + 1;
					while ($j < $tokenCount && is_array($tokens[$j]) && $tokens[$j][0] === T_WHITESPACE) {
						$ws = $tokens[$j][1];
						if (strpos($ws, "\n") !== false || strpos($ws, "\r") !== false) {
							$followingHasNewline = true;
						}
						// Always consume this whitespace; we will re-insert at most one newline below
						$j++;
					}
					// Advance main loop to last consumed whitespace (or stay on comment if none)
					$i = $j - 1;

					// Keep at most one newline if the comment or following whitespace had a newline
					if ($commentHasNewline || $followingHasNewline) {
						if ($output === '' || substr($output, -strlen($newline)) !== $newline) {
							$output .= $newline;
						}
					}

					continue; // removed code-like PHP comment and adjusted whitespace
				} else {
					$output .= $text; // keep normal text comment
				}
			} else {
				$output .= $text;
			}
		} else {
			$output .= $token;
		}
	}

	// 🔹 Extra step: Handle HTML comments <!-- ... -->
	// 1) Remove full-line code-like HTML comments including surrounding spaces and trailing newline
	$output = preg_replace_callback('/^[ \t]*<!--(.*?)-->[ \t]*(\r?\n)?/m', function ($matches) use (&$allComments) {
		$comment = $matches[0];
		$commentContent = $matches[1];
		$allComments[] = $comment;
		if (isCodeLikeHtmlComment($commentContent)) {
			return ''; // remove entire line with the comment
		}
		return $comment; // keep as-is
	}, $output);

	// 2) Remove inline code-like HTML comments and strip immediate surrounding spaces (same line)
	$output = preg_replace_callback('/[ \t]*<!--(.*?)-->[ \t]*/s', function ($matches) use (&$allComments) {
		$comment = $matches[0];
		$commentContent = $matches[1];
		$allComments[] = $comment;
		if (isCodeLikeHtmlComment($commentContent)) {
			return ''; // remove inline comment and adjacent spaces
		}
		return $comment; // keep normal text HTML comment
	}, $output);

	// Final whitespace cleanup: remove trailing spaces before newlines
	$output = preg_replace('/[ \t]+(\r?\n)/', '$1', $output);
	// Remove whitespace-only lines
	$output = preg_replace('/^[ \t]+$/m', '', $output);
	// Collapse multiple blank lines to a single blank line
	$output = preg_replace('/(\r?\n){2,}/', $newline . $newline, $output);

	return $output;
}

// Handle file upload
$allComments = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$uploads = [];

	// Collect from multiple files input
	if (isset($_FILES['phpfiles']) && isset($_FILES['phpfiles']['name']) && is_array($_FILES['phpfiles']['name'])) {
		for ($i = 0; $i < count($_FILES['phpfiles']['name']); $i++) {
			if ($_FILES['phpfiles']['error'][$i] === UPLOAD_ERR_OK) {
				$uploads[] = [
					'tmp_name' => $_FILES['phpfiles']['tmp_name'][$i],
					'name' => $_FILES['phpfiles']['name'][$i],
				];
			}
		}
	}

	// Collect from directory input (webkitdirectory)
	if (isset($_FILES['phpdir']) && isset($_FILES['phpdir']['name']) && is_array($_FILES['phpdir']['name'])) {
		for ($i = 0; $i < count($_FILES['phpdir']['name']); $i++) {
			if ($_FILES['phpdir']['error'][$i] === UPLOAD_ERR_OK) {
				$uploads[] = [
					'tmp_name' => $_FILES['phpdir']['tmp_name'][$i],
					'name' => $_FILES['phpdir']['name'][$i], // may contain relative path
				];
			}
		}
	}

	// Filter to only .php files
	$phpFiles = [];
	foreach ($uploads as $u) {
		$relName = str_replace('\\', '/', $u['name']);
		$ext = strtolower(pathinfo($relName, PATHINFO_EXTENSION));
		if ($ext === 'php') {
			$phpFiles[] = [
				'tmp_name' => $u['tmp_name'],
				'name' => ltrim($relName, '/'),
			];
		}
	}

	if (count($phpFiles) === 0) {
		http_response_code(400);
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'No PHP files found in the upload.';
		exit;
	}

	// Single file: return cleaned PHP directly
	if (count($phpFiles) === 1) {
		$file = $phpFiles[0];
		$code = file_get_contents($file['tmp_name']);
		$commentsBucket = [];
		$cleanCode = removeComments($code, $commentsBucket);

		$base = basename($file['name']);
		$dotPos = strrpos($base, '.');
		$stem = $dotPos !== false ? substr($base, 0, $dotPos) : $base;
		$downloadName = $stem . '.clean.php';

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="' . $downloadName . '"');
		header('Content-Transfer-Encoding: binary');
		header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Content-Length: ' . strlen($cleanCode));
		echo $cleanCode;
		exit;
	}

	// Multiple files: zip and return
	if (!class_exists('ZipArchive')) {
		http_response_code(500);
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'ZipArchive PHP extension is required to download multiple files.';
		exit;
	}

	$zip = new ZipArchive();
	$tmpZip = tempnam(sys_get_temp_dir(), 'cleanphp_');
	if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
		http_response_code(500);
		header('Content-Type: text/plain; charset=UTF-8');
		echo 'Failed to create ZIP archive.';
		exit;
	}

	foreach ($phpFiles as $file) {
		$code = file_get_contents($file['tmp_name']);
		$commentsBucket = [];
		$cleanCode = removeComments($code, $commentsBucket);

		$rel = $file['name'];
		$rel = str_replace('\\', '/', $rel);
		$rel = ltrim($rel, '/');
		if ($rel === '' || substr($rel, -1) === '/') {
			continue; // skip invalid names
		}
		$dir = trim(dirname($rel), './');
		$base = basename($rel);
		$dotPos = strrpos($base, '.');
		$stem = $dotPos !== false ? substr($base, 0, $dotPos) : $base;
		$zipPath = ($dir ? ($dir . '/') : '') . $stem . '.php';

		$zip->addFromString($zipPath, $cleanCode);
	}

	$zip->close();

	header('Content-Description: File Transfer');
	header('Content-Type: application/zip');
	header('Content-Disposition: attachment; filename="cleaned_php_files.zip"');
	header('Content-Transfer-Encoding: binary');
	header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
	header('Pragma: no-cache');
	header('Expires: 0');
	header('Content-Length: ' . filesize($tmpZip));
	readfile($tmpZip);
	@unlink($tmpZip);
	exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Comment Cleaner</title>
	<style>
		:root {
			--bg: #0f172a;
			--panel: #111827;
			--muted: #9ca3af;
			--text: #e5e7eb;
			--primary: #6366f1;
			--primary-2: #8b5cf6;
			--accent: #22d3ee;
		}
		* { box-sizing: border-box; }
		body {
			margin: 0;
			min-height: 100vh;
			background: radial-gradient(1200px 600px at 10% -10%, rgba(99,102,241,0.15), transparent),
				radial-gradient(1000px 500px at 100% 0%, rgba(34,211,238,0.12), transparent),
				var(--bg);
			color: var(--text);
			font-family: system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, "Apple Color Emoji", "Segoe UI Emoji";
			padding: 40px 16px;
		}
		.container {
			max-width: 880px;
			margin: 0 auto;
		}
		.header {
			text-align: center;
			margin-bottom: 24px;
		}
		.header h1 {
			margin: 0;
			font-size: 28px;
			font-weight: 700;
		}
		.header p { color: var(--muted); margin-top: 8px; }
		.card {
			background: linear-gradient(180deg, rgba(255,255,255,0.04), rgba(255,255,255,0.02));
			border: 1px solid rgba(255,255,255,0.08);
			border-radius: 16px;
			box-shadow: 0 10px 30px rgba(0,0,0,0.35);
			padding: 24px;
		}
		.row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
		@media (max-width: 720px) { .row { grid-template-columns: 1fr; } }
		.label { font-weight: 600; margin-bottom: 8px; display: inline-block; }
		.input {
			width: 100%;
			padding: 14px;
			border-radius: 12px;
			border: 1px dashed rgba(255,255,255,0.18);
			background: rgba(255,255,255,0.03);
			color: var(--text);
		}
		.hint { color: var(--muted); font-size: 12px; margin-top: 8px; }
		.actions { margin-top: 20px; display: flex; gap: 12px; align-items: center; }
		.button {
			appearance: none;
			border: none;
			padding: 12px 18px;
			border-radius: 12px;
			font-weight: 700;
			cursor: pointer;
			background: linear-gradient(135deg, var(--primary), var(--primary-2));
			color: white;
			box-shadow: 0 6px 18px rgba(99,102,241,0.35);
		}
		.badge { font-size: 12px; color: var(--muted); }
	</style>
</head>
<body style="font-family: Arial; padding:20px;">
	<div class="container">
		<div class="header">
			<h1>PHP Comment Cleaner</h1>
			<p>Upload one file, multiple files, or a whole directory. We'll clean code-like comments and download the result.</p>
		</div>
		<div class="card">
			<form method="POST" enctype="multipart/form-data">
				<div class="row">
					<div>
						<label class="label">Select PHP file(s)</label>
						<input class="input" type="file" name="phpfiles[]" accept=".php" multiple>
						<div class="hint">You can choose multiple .php files at once.</div>
					</div>
					<div>
						<label class="label">Or select a directory</label>
						<input class="input" type="file" name="phpdir[]" webkitdirectory directory>
						<div class="hint">Directory selection is supported in Chromium-based browsers.</div>
					</div>
				</div>
				<div class="actions">
					<button class="button" type="submit">Clean & Download</button>
					<span class="badge">Only .php files are processed. Multiple files or directories will download as a ZIP.</span>
				</div>
			</form>
		</div>
	</div>
</body>
</html>
