# PHP Comment Remover

This repository contains Python scripts to intelligently remove comments from PHP files.

## Scripts

### 1. `remove_comments_smart.py` (Recommended)
A smart script that keeps useful descriptive comments while removing code-related ones.

### 2. `remove_comments_simple.py`
A simple script that removes all comments.

## Features

### Smart Comment Removal (`remove_comments_smart.py`)
**KEEPS:**
- Simple descriptive comments like `//fetch ai apprenticeship within district`
- Comments that explain what the code does

**REMOVES:**
- Comments with code characters like `<>()[]{} etc.`
- HTML comments: `<!-- -->`
- Multi-line comments: `/* */`
- Section separators like `<!-- =============== AI Dashboard =================== -->`

### Simple Comment Removal (`remove_comments_simple.py`)
Removes all types of comments:
- **HTML comments**: `<!-- -->`
- **JavaScript single-line comments**: `//`
- **JavaScript multi-line comments**: `/* */`
- **PHP single-line comments**: `//` and `#`
- **PHP multi-line comments**: `/* */`

## Usage

### Smart Comment Removal (Recommended)
```bash
python remove_comments_smart.py something.php
```

This will create `something_smart_cleaned.php` with intelligent comment removal.

### Simple Comment Removal
```bash
python remove_comments_simple.py something.php
```

This will create `something_no_comments.php` with all comments removed.

### Specify Output File
```bash
python remove_comments_smart.py something.php cleaned.php
```

## Requirements

- Python 3.6 or higher
- No additional packages required (uses only built-in modules)

## How Smart Comment Removal Works

1. **Analyzes each comment** to determine if it's useful
2. **Keeps simple descriptive comments** that explain functionality
3. **Removes comments with code characters** that are likely not useful
4. **Removes HTML and multi-line comments** completely
5. **Preserves code structure** and meaningful documentation

## Examples

### Before (with mixed comments):
```php
<?php
//fetch ai apprenticeship within district
$var = "value";

<!-- =============== AI Dashboard =================== -->
<script>
//code to change the table accordngly...
var x = 1;
</script>

/* Multi-line comment block */
?>
```

### After Smart Removal:
```php
<?php
//fetch ai apprenticeship within district
$var = "value";

<script>
//code to change the table accordngly...
var x = 1;
</script>

?>
```

**Note:** The smart script kept the useful comments but removed the HTML section separator and multi-line comment.

## Safety Features

- Preserves URLs and special patterns
- Maintains code structure
- Creates backup files (original file is not modified)
- Handles encoding properly
- Intelligently preserves meaningful comments

## Notes

- The original file is never modified
- Smart script creates files with `_smart_cleaned` suffix
- Simple script creates files with `_no_comments` suffix
- Empty lines are cleaned up but structure is maintained
- The script is safe to run multiple times on the same file 