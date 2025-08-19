<?php
// Simple descriptive comment that should be kept
$title = "Comment Removal Test Page";

// Another useful comment explaining the purpose
$description = "This page tests different types of comments for removal";

// HTML comment that should be removed

// Comment with code characters that should be removed

// Comment with parentheses that should be removed

// Comment with brackets that should be removed

// Comment with special characters that should be removed

// Simple comment that should be kept
// This is a useful description

// Another simple comment
// Testing comment removal functionality

// Comment with equals signs that should be removed

// Comment with dashes that should be removed

// Comment with asterisks that should be removed

// Comment with underscores that should be removed

// Simple comment that should be kept
// This comment explains the next line of code

$current_time = date('Y-m-d H:i:s');

// Comment that should be kept
// Display the current time

echo "<!DOCTYPE html>";
echo "<html>";
echo "<head>";
echo "<title>$title</title>";
echo "<meta charset='UTF-8'>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; margin: 40px; }";
echo ".section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; }";
echo ".kept { background-color: #d4edda; border-color: #c3e6cb; }";
echo ".removed { background-color: #f8d7da; border-color: #f5c6cb; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<h1>$title</h1>";
echo "<p>$description</p>";

echo "<div class='section kept'>";
echo "<h2>Comments That Should Be Kept:</h2>";
echo "<ul>";
echo "<li>
echo "<li>
echo "<li>
echo "<li>
echo "<li>
echo "</ul>";
echo "</div>";

echo "<div class='section removed'>";
echo "<h2>Comments That Should Be Removed:</h2>";
echo "<ul>";
echo "<li>HTML comments: &lt;!-- =============== TEST SECTION =================== --&gt;</li>";
echo "<li>Comments with code:
echo "<li>Comments with parentheses:
echo "<li>Comments with brackets:
echo "<li>Comments with special chars:
echo "<li>Multi-line comments: </li>";
echo "<li>Separator comments:
echo "<li>Dash comments:
echo "<li>Asterisk comments:
echo "<li>Underscore comments:
echo "</ul>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Current Time:</h2>";
echo "<p><strong>$current_time</strong></p>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Test Results:</h2>";
echo "<p>After running the comment removal script:</p>";
echo "<ul>";
echo "<li>✅ Simple descriptive comments should be preserved</li>";
echo "<li>❌ Comments with code characters should be removed</li>";
echo "<li>❌ HTML comments should be removed</li>";
echo "<li>❌ Multi-line comments should be removed</li>";
echo "<li>❌ Separator-style comments should be removed</li>";
echo "</ul>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>How to Test:</h2>";
echo "<ol>";
echo "<li>Save this file as <code>test_comments.php</code></li>";
echo "<li>Open it in your browser to see the current state</li>";
echo "<li>Run: <code>python remove_comments_smart.py test_comments.php</code></li>";
echo "<li>Check the generated <code>test_comments_smart_cleaned.php</code></li>";
echo "<li>Compare the results to see what was kept vs. removed</li>";
echo "</ol>";
echo "</div>";

echo "<div class='section'>";
echo "<h2>Expected Behavior:</h2>";
echo "<p>The smart comment removal script should:</p>";
echo "<ul>";
echo "<li><strong>Keep:</strong> Simple descriptive comments that explain functionality</li>";
echo "<li><strong>Remove:</strong> Comments with code characters, HTML comments, multi-line comments</li>";
echo "<li><strong>Remove:</strong> Decorative separator comments with repeated characters</li>";
echo "</ul>";
echo "</div>";

echo "
<script>
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws:
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
</script>
</body>";
echo "</html>";
?>