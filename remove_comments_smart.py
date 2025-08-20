#!/usr/bin/env python3
"""
Smart PHP Comment Remover Script with Built-in Web UI

This script intelligently removes comments from PHP files:
- KEEPS: Simple descriptive comments like "//fetch ai apprenticeship within district"
- REMOVES: Comments with code-related characters like <>()[]{} etc.
- REMOVES: HTML comments <!-- -->
- REMOVES: Multi-line comments /* */

Usage: 
- CLI: python remove_comments_smart.py input_file.php [output_file.php]
- Web UI: python remove_comments_smart.py --web
"""

import re
import sys
import os
import json
import zipfile
import tempfile
from flask import Flask, request, jsonify, send_file, render_template_string
from flask_cors import CORS
from werkzeug.utils import secure_filename
import datetime

# HTML template for the web UI
HTML_TEMPLATE = """
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Comment Remover</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #333; margin-bottom: 10px; }
        .header p { color: #666; }
        .subtitle { color: #007bff; font-weight: 500; margin-top: 5px; }
        .upload-area { 
            background: white; 
            border: 2px dashed #ddd; 
            border-radius: 10px; 
            padding: 40px; 
            text-align: center; 
            margin-bottom: 30px;
            transition: all 0.3s ease;
        }
        .upload-area:hover { border-color: #007bff; }
        .upload-area.dragover { border-color: #007bff; background: #f8f9ff; }
        .file-input { display: none; }
        .upload-btn { 
            background: #007bff; 
            color: white; 
            padding: 12px 30px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            font-size: 16px;
            transition: background 0.3s ease;
        }
        .upload-btn:hover { background: #0056b3; }
        .upload-buttons { display: flex; gap: 10px; justify-content: center; margin-top: 20px; }
        .dir-btn { background: #28a745; }
        .dir-btn:hover { background: #218838; }
        .upload-info { margin-top: 15px; color: #666; font-size: 14px; }
        .options { background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .options h3 { margin-bottom: 15px; color: #333; }
        .option-group { margin-bottom: 15px; }
        .option-group label { display: block; margin-bottom: 5px; color: #555; }
        .checkbox { margin-right: 8px; }
        .progress { display: none; background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .progress-bar { 
            width: 100%; 
            height: 20px; 
            background: #eee; 
            border-radius: 10px; 
            overflow: hidden; 
            margin-bottom: 10px;
        }
        .progress-fill { 
            height: 100%; 
            background: #007bff; 
            width: 0%; 
            transition: width 0.3s ease;
        }
        .results { background: white; padding: 20px; border-radius: 10px; display: none; }
        .result-item { 
            border: 1px solid #ddd; 
            padding: 15px; 
            margin-bottom: 10px; 
            border-radius: 5px;
            display: flex; 
            justify-content: space-between; 
            align-items: center;
        }
        .download-btn { 
            background: #28a745; 
            color: white; 
            padding: 8px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer;
            text-decoration: none;
        }
        .download-btn:hover { background: #218838; }
        .zip-btn { background: #ff6b35; }
        .zip-btn:hover { background: #e55a2b; }
        .zip-info { color: #ff6b35; font-weight: bold; }
        .error { color: #dc3545; margin-top: 10px; }
        .success { color: #28a745; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Smart Comment Remover</h1>
            <p>Intelligently remove comments while keeping useful ones</p>
            <p class="subtitle">Process individual files or entire directories</p>
        </div>

        <div class="upload-area" id="uploadArea">
            <h3>📁 Drop files here or click to select</h3>
            <p>Supports: PHP, HTML, JavaScript, CSS, TXT files</p>
            <div class="upload-buttons">
                <input type="file" id="fileInput" class="file-input" multiple accept=".php,.html,.htm,.js,.css,.txt">
                <input type="file" id="dirInput" class="file-input" webkitdirectory directory multiple>
                <button class="upload-btn" onclick="document.getElementById('fileInput').click()">Choose Files</button>
                <button class="upload-btn dir-btn" onclick="document.getElementById('dirInput').click()">Choose Directory</button>
            </div>
            <p class="upload-info" id="uploadInfo">Select individual files or entire directories</p>
        </div>

        <div class="options">
            <h3>⚙️ Processing Options</h3>
            <div class="option-group">
                <label><input type="checkbox" class="checkbox" id="removeHtml" checked> Remove HTML comments (<!-- -->)</label>
            </div>
            <div class="option-group">
                <label><input type="checkbox" class="checkbox" id="removeMultiline" checked> Remove multi-line comments (/* */)</label>
            </div>
            <div class="option-group">
                <label><input type="checkbox" class="checkbox" id="removeCode" checked> Remove comments with code characters</label>
            </div>
            <div class="option-group">
                <label><input type="checkbox" class="checkbox" id="cleanEmpty" checked> Clean excessive empty lines</label>
            </div>
        </div>

        <div class="progress" id="progress">
            <h3>🔄 Processing Files...</h3>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <p id="progressText">0% Complete</p>
        </div>

        <div class="results" id="results">
            <h3>✅ Processing Complete</h3>
            <div id="resultList"></div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const dirInput = document.getElementById('dirInput');
        const progress = document.getElementById('progress');
        const results = document.getElementById('results');
        const resultList = document.getElementById('resultList');
        const uploadInfo = document.getElementById('uploadInfo');

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const files = e.dataTransfer.files;
            processFiles(files);
        });

        fileInput.addEventListener('change', (e) => {
            const files = e.target.files;
            uploadInfo.textContent = `Selected ${files.length} file(s)`;
            processFiles(files);
        });

        dirInput.addEventListener('change', (e) => {
            const files = e.target.files;
            uploadInfo.textContent = `Selected directory with ${files.length} file(s)`;
            processFiles(files);
        });

        function processFiles(files) {
            if (files.length === 0) return;

            const formData = new FormData();
            for (let file of files) {
                formData.append('files', file);
            }

            // Add options
            const options = {
                removeHtmlComments: document.getElementById('removeHtml').checked,
                removeMultilineComments: document.getElementById('removeMultiline').checked,
                removeCodeComments: document.getElementById('removeCode').checked,
                cleanEmptyLines: document.getElementById('cleanEmpty').checked
            };
            formData.append('options', JSON.stringify(options));

            // Show progress
            progress.style.display = 'block';
            results.style.display = 'none';

            // Simulate progress
            let progressValue = 0;
            const progressInterval = setInterval(() => {
                progressValue += 10;
                document.getElementById('progressFill').style.width = progressValue + '%';
                document.getElementById('progressText').textContent = progressValue + '% Complete';
                if (progressValue >= 100) {
                    clearInterval(progressInterval);
                }
            }, 200);

            // Send files to server
            fetch('/api/process-files', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearInterval(progressInterval);
                document.getElementById('progressFill').style.width = '100%';
                document.getElementById('progressText').textContent = '100% Complete';
                
                setTimeout(() => {
                    progress.style.display = 'none';
                    showResults(data);
                }, 500);
            })
            .catch(error => {
                clearInterval(progressInterval);
                progress.style.display = 'none';
                alert('Error processing files: ' + error.message);
            });
        }

        function showResults(data) {
            if (data.success) {
                results.style.display = 'block';
                resultList.innerHTML = '';

                data.results.forEach(result => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'result-item';
                    const isZip = result.isZip || false;
                    const downloadText = isZip ? 'Download ZIP' : 'Download';
                    const downloadClass = isZip ? 'download-btn zip-btn' : 'download-btn';
                    
                    resultItem.innerHTML = `
                        <div>
                            <strong>${result.originalName}</strong><br>
                            <small>Size: ${result.originalSize} → ${result.cleanedSize} bytes</small>
                            ${isZip ? '<br><small class="zip-info">📦 Contains all cleaned files</small>' : ''}
                        </div>
                        <a href="/api/download/${result.cleanedName}" class="${downloadClass}">${downloadText}</a>
                    `;
                    resultList.appendChild(resultItem);
                });
            } else {
                alert('Error: ' + data.error);
            }
        }
    </script>
</body>
</html>
"""

def is_simple_comment(comment_text):
    """
    Check if a comment is simple and descriptive (should be kept)
    Returns True if comment should be kept, False if it should be removed
    """
    # Remove the comment markers to get just the content
    clean_text = comment_text.strip()
    
    # If it's empty, remove it
    if not clean_text:
        return False
    
    # Check if comment contains links, URLs, or iframe tags FIRST (should be removed)
    # These patterns indicate the comment contains a link/URL, not code with a link
    link_patterns = [
        r'https?://',  # http:// or https://
        r'ftp://',     # ftp://
        r'file://',    # file://
        r'<iframe',    # <iframe tag
        r'</iframe>',  # </iframe tag
        r'iframe',     # iframe (case insensitive)
        r'<a\s+href',  # <a href tag
        r'www\.',      # www.
        r'\.com',      # .com
        r'\.org',      # .org
        r'\.net',      # .net
        r'\.io',       # .io
        r'powerbi\.com', # powerbi.com
        r'powerbi',    # powerbi (case insensitive)
        r'frameborder', # frameborder attribute
        r'allowFullScreen', # allowFullScreen attribute
        r'src=',       # src attribute
        r'width=',     # width attribute
        r'height=',    # height attribute
        r'title=',     # title attribute
    ]
    
    print(f"DEBUG: Checking comment: '{clean_text}'")
    
    # Check if comment contains links, URLs, or iframe tags
    link_found = False
    for pattern in link_patterns:
        if re.search(pattern, clean_text, re.IGNORECASE):
            print(f"DEBUG: Pattern '{pattern}' matched")
            link_found = True
            break
    
    # If a link is found, check if this is a PURE link comment or a useful comment with a link
    if link_found:
        # Check if this is a pure link comment (just a URL/iframe, no useful description)
        pure_link_patterns = [
            r'^\s*(?:https?://|ftp://|file://|www\.|\.com|\.org|\.net|\.io)',  # Starts with URL
            r'^\s*<iframe.*?>\s*$',  # Just an iframe tag
            r'^\s*<a\s+href.*?>\s*$',  # Just an anchor tag
            r'^\s*powerbi\.com',  # Just powerbi.com
        ]
        
        for pattern in pure_link_patterns:
            if re.search(pattern, clean_text, re.IGNORECASE | re.DOTALL):
                print(f"DEBUG: Pure link pattern '{pattern}' matched - removing comment")
                return False
        
        # If it's not a pure link comment, check if it has useful descriptive text
        # Remove common link patterns to see what's left
        temp_text = clean_text
        for pattern in link_patterns:
            temp_text = re.sub(pattern, '', temp_text, flags=re.IGNORECASE)
        
        # If there's meaningful text left after removing links, check if it's actually useful
        meaningful_text = temp_text.strip()
        
        # Check if the remaining text contains code-like patterns that indicate it should be removed
        code_like_patterns = [
            r'window\.location',  # JavaScript navigation
            r'location\.href',     # JavaScript location
            r'\.href\s*=',         # href assignment
            r'\.src\s*=',          # src assignment
            r'\.action\s*=',       # action assignment
            r'function\s*\(',      # function definition
            r'if\s*\(',            # if statement
            r'for\s*\(',           # for loop
            r'while\s*\(',         # while loop
            r'var\s+',             # variable declaration
            r'let\s+',             # let declaration
            r'const\s+',           # const declaration
            r'console\.',          # console methods
            r'alert\s*\(',         # alert function
            r'confirm\s*\(',       # confirm function
            r'prompt\s*\(',        # prompt function
            r'\.addEventListener', # event listener
            r'\.onclick',          # onclick handler
            r'\.submit',           # form submit
            r'\.preventDefault',   # prevent default
            r'\.stopPropagation', # stop propagation
            r'return\s+',          # return statement
            r'throw\s+',           # throw statement
            r'try\s*{',            # try block
            r'catch\s*\(',         # catch block
            r'finally\s*{',        # finally block
        ]
        
        # If the remaining text contains code-like patterns, remove the comment
        for pattern in code_like_patterns:
            if re.search(pattern, meaningful_text, re.IGNORECASE):
                print(f"DEBUG: Code-like pattern '{pattern}' found in remaining text - removing comment")
                return False
        
        # If there's substantial descriptive text AND no code-like patterns, keep it
        if len(meaningful_text) > 10:
            print(f"DEBUG: Comment has meaningful text after removing links: '{meaningful_text}' - keeping comment")
            return True
        else:
            print(f"DEBUG: Comment is mostly just links - removing comment")
            return False
    
    print(f"DEBUG: No link patterns matched - keeping comment")
    
    # Check if comment contains code-related characters that indicate it should be removed
    code_chars = ['<', '>', '(', ')', '[', ']', '{', '}', '=', '+', '-', '*', '/', '\\', '|', '&', '^', '%', '$', '@', '!', '?', ';', ':', '"', "'", '`']
    
    # If comment contains any code-related characters, remove it
    for char in code_chars:
        if char in clean_text:
            return False
    
    # If comment is very short (less than 3 chars), it's probably not useful
    if len(clean_text) < 3:
        return False
    
    # If comment is just repeated characters or separators, remove it
    if re.match(r'^[=\-\*_]+$', clean_text):
        return False
    
    # If comment looks like a section header with separators, remove it
    if re.match(r'^[=\-\*_]+.*[=\-\*_]+$', clean_text):
        return False
    
    # Keep simple descriptive comments
    return True

def remove_html_comments_intelligent(content):
    """Intelligently remove HTML comments - keep useful ones, remove decorative ones"""
    def should_keep_html_comment(comment_text):
        """Check if HTML comment should be kept"""
        # Remove <!-- and --> to get just the content
        clean_text = comment_text.strip()
        
        # If it's empty, remove it
        if not clean_text:
            return False
        
        # If comment is very short (less than 3 chars), it's probably not useful
        if len(clean_text) < 3:
            return False
        
        # Check if comment contains code-related characters that indicate it should be removed
        code_chars = ['<', '>', '(', ')', '[', ']', '{', '}', '=', '+', '-', '*', '/', '\\', '|', '&', '^', '%', '$', '@', '!', '?', ';', ':', '"', "'", '`']
        
        # If comment contains any code-related characters, remove it
        for char in code_chars:
            if char in clean_text:
                return False
        
        # If comment is just repeated characters or separators, remove it
        if re.match(r'^[=\-\*_]+$', clean_text):
            return False
        
        # If comment looks like a section header with separators, remove it
        if re.match(r'^[=\-\*_]+.*[=\-\*_]+$', clean_text):
            return False
        
        # Check if comment contains links, URLs, or iframe tags (should be removed)
        link_patterns = [
            r'https?://',  # http:// or https://
            r'ftp://',     # ftp://
            r'file://',    # file://
            r'<iframe',    # <iframe tag
            r'<a\s+href',  # <a href tag
            r'www\.',      # www.
            r'\.com',      # .com
            r'\.org',      # .org
            r'\.net',      # .net
            r'\.io',       # .io
            r'powerbi\.com', # powerbi.com
            r'frameborder', # frameborder attribute
            r'allowFullScreen', # allowFullScreen attribute
            r'src=',       # src attribute
            r'width=',     # width attribute
            r'height=',    # height attribute
            r'title=',     # title attribute
        ]
        
        # Check if comment contains links, URLs, or iframe tags
        link_found = False
        for pattern in link_patterns:
            if re.search(pattern, clean_text, re.IGNORECASE):
                link_found = True
                break
        
        # If a link is found, check if this is a PURE link comment or a useful comment with a link
        if link_found:
            # Check if this is a pure link comment (just a URL/iframe, no useful description)
            pure_link_patterns = [
                r'^\s*(?:https?://|ftp://|file://|www\.|\.com|\.org|\.net|\.io)',  # Starts with URL
                r'^\s*<iframe.*?>\s*$',  # Just an iframe tag
                r'^\s*<a\s+href.*?>\s*$',  # Just an anchor tag
                r'^\s*powerbi\.com',  # Just powerbi.com
            ]
            
            for pattern in pure_link_patterns:
                if re.search(pattern, clean_text, re.IGNORECASE | re.DOTALL):
                    return False
            
            # If it's not a pure link comment, check if it has useful descriptive text
            # Remove common link patterns to see what's left
            temp_text = clean_text
            for pattern in link_patterns:
                temp_text = re.sub(pattern, '', temp_text, flags=re.IGNORECASE)
            
            # If there's meaningful text left after removing links, keep it
            meaningful_text = temp_text.strip()
            if len(meaningful_text) > 10:  # If there's substantial descriptive text
                return True
            else:
                return False
        
        # Keep simple descriptive comments like "test this", "section header", etc.
        return True
    
    # Find all HTML comments and process them intelligently
    def process_html_comment(match):
        comment_content = match.group(1)  # Content between <!-- and -->
        if should_keep_html_comment(comment_content):
            # Keep the comment
            return f"<!--{comment_content}-->"
        else:
            # Remove the comment
            return ""
    
    # Use regex to find HTML comments and process them
    return re.sub(r'<!--(.*?)-->', process_html_comment, content, flags=re.DOTALL)

def remove_html_comments(content):
    """Remove HTML comments <!-- --> (legacy function - use remove_html_comments_intelligent instead)"""
    return remove_html_comments_intelligent(content)

def remove_multiline_comments_intelligent(content):
    """Intelligently remove multi-line comments - keep useful ones, remove decorative ones"""
    def should_keep_multiline_comment(comment_text):
        """Check if multi-line comment should be kept"""
        # Remove /* and */ to get just the content
        clean_text = comment_text.strip()
        
        # If it's empty, remove it
        if not clean_text:
            return False
        
        # If comment is very short (less than 3 chars), it's probably not useful
        if len(clean_text) < 3:
            return False
        
        # Check if comment contains code-related characters that indicate it should be removed
        code_chars = ['<', '>', '(', ')', '[', ']', '{', '}', '=', '+', '-', '*', '/', '\\', '|', '&', '^', '%', '$', '@', '!', '?', ';', ':', '"', "'", '`']
        
        # If comment contains any code-related characters, remove it
        for char in code_chars:
            if char in clean_text:
                return False
        
        # If comment is just repeated characters or separators, remove it
        if re.match(r'^[=\-\*_]+$', clean_text):
            return False
        
        # If comment looks like a section header with separators, remove it
        if re.match(r'^[=\-\*_]+.*[=\-\*_]+$', clean_text):
            return False
        
        # Check if comment contains links, URLs, or iframe tags (should be removed)
        link_patterns = [
            r'https?://',  # http:// or https://
            r'ftp://',     # ftp://
            r'file://',    # file://
            r'<iframe',    # <iframe tag
            r'<a\s+href',  # <a href tag
            r'www\.',      # www.
            r'\.com',      # .com
            r'\.org',      # .org
            r'\.net',      # .net
            r'\.io',       # .io
            r'powerbi\.com', # powerbi.com
            r'frameborder', # frameborder attribute
            r'allowFullScreen', # allowFullScreen attribute
            r'src=',       # src attribute
            r'width=',     # width attribute
            r'height=',    # height attribute
            r'title=',     # title attribute
        ]
        
        # Check if comment contains links, URLs, or iframe tags
        link_found = False
        for pattern in link_patterns:
            if re.search(pattern, clean_text, re.IGNORECASE):
                link_found = True
                break
        
        # If a link is found, check if this is a PURE link comment or a useful comment with a link
        if link_found:
            # Check if this is a pure link comment (just a URL/iframe, no useful description)
            pure_link_patterns = [
                r'^\s*(?:https?://|ftp://|file://|www\.|\.com|\.org|\.net|\.io)',  # Starts with URL
                r'^\s*<iframe.*?>\s*$',  # Just an iframe tag
                r'^\s*<a\s+href.*?>\s*$',  # Just an anchor tag
                r'^\s*powerbi\.com',  # Just powerbi.com
            ]
            
            for pattern in pure_link_patterns:
                if re.search(pattern, clean_text, re.IGNORECASE | re.DOTALL):
                    return False
            
            # If it's not a pure link comment, check if it has useful descriptive text
            # Remove common link patterns to see what's left
            temp_text = clean_text
            for pattern in link_patterns:
                temp_text = re.sub(pattern, '', temp_text, flags=re.IGNORECASE)
            
            # If there's meaningful text left after removing links, keep it
            meaningful_text = temp_text.strip()
            if len(meaningful_text) > 10:  # If there's substantial descriptive text
                return True
            else:
                return False
        
        # Keep simple descriptive comments like "this is a test", "section header", etc.
        return True
    
    # Find all multi-line comments and process them intelligently
    def process_multiline_comment(match):
        comment_content = match.group(1)  # Content between /* and */
        if should_keep_multiline_comment(comment_content):
            # Keep the comment
            return f"/*{comment_content}*/"
        else:
            # Remove the comment
            return ""
    
    # Use regex to find multi-line comments and process them
    return re.sub(r'/\*(.*?)\*/', process_multiline_comment, content, flags=re.DOTALL)

def remove_multiline_comments(content):
    """Remove multi-line comments /* */ (legacy function - use remove_multiline_comments_intelligent instead)"""
    return remove_multiline_comments_intelligent(content)

def process_single_line_comments(content):
    """Process single-line comments intelligently"""
    # Split content into lines, but preserve original line endings
    original_lines = content.splitlines(keepends=True)
    result_lines = []
    
    for line in original_lines:
        # Remove the line ending for processing
        line_content = line.rstrip('\r\n')
        
        # Check for // comments
        if '//' in line_content:
            # Split the line at //
            parts = line_content.split('//', 1)
            code_part = parts[0]  # Keep original spacing
            comment_part = parts[1] if len(parts) > 1 else ""
            
            # Check if this is a URL or special case (only if it's actual code, not a comment)
            if code_part.strip() and any(url_indicator in code_part for url_indicator in ['http://', 'https://', 'ftp://', 'file://']):
                # This is actual code with a URL, keep the line as is
                result_lines.append(line_content)
                continue
            
            # Check if this is an HTML attribute with a URL (like src="https://...")
            # This prevents treating HTML attributes as comments
            html_attrs = [
                'src=', 'href=', 'action=', 'data-', 'url=', 'link=', 'api=', 'cdn=',
                'script=', 'style=', 'img=', 'video=', 'audio=', 'source=', 'track=',
                'embed=', 'object=', 'param=', 'iframe=', 'frame=', 'form=', 'input='
            ]
            if any(attr in code_part.lower() for attr in html_attrs):
                # This is an HTML attribute, not a comment - keep the line as is
                result_lines.append(line_content)
                continue
            
            # Additional protection: Check if this looks like HTML content (not a comment)
            # This prevents treating HTML tags and attributes as comments
            if any(html_indicator in code_part.lower() for html_indicator in ['<script', '<link', '<img', '<a ', '<form', '<iframe', '<embed', '<object']):
                # This is HTML content, not a comment - keep the line as is
                result_lines.append(line_content)
                continue
            
            # SIMPLIFIED LOGIC: If comment contains URL or code patterns, remove it
            # Check if comment contains URLs
            url_patterns = [r'https?://', r'ftp://', r'file://', r'www\.', r'\.com', r'\.org', r'\.net', r'\.io', r'\.gov', r'\.edu']
            has_url = any(re.search(pattern, comment_part, re.IGNORECASE) for pattern in url_patterns)
            
            # Check if comment contains code patterns
            code_patterns = [
                r'window\.location', r'location\.href', r'\.href\s*=', r'\.src\s*=', r'\.action\s*=',
                r'function\s*\(', r'if\s*\(', r'for\s*\(', r'while\s*\(', r'var\s+', r'let\s+', r'const\s+',
                r'console\.', r'alert\s*\(', r'confirm\s*\(', r'prompt\s*\(', r'\.addEventListener',
                r'\.onclick', r'\.submit', r'\.preventDefault', r'\.stopPropagation', r'return\s+', r'throw\s+',
                r'try\s*{', r'catch\s*\(', r'finally\s*{', r'<iframe', r'<script', r'<a\s+href'
            ]
            has_code = any(re.search(pattern, comment_part, re.IGNORECASE) for pattern in code_patterns)
            
            # If comment has URL or code patterns, remove it
            if has_url or has_code:
                print(f"DEBUG: Comment contains URL or code patterns - removing: '{comment_part}'")
                if code_part.strip():
                    # Keep the code part, remove the comment
                    result_lines.append(code_part.rstrip())
                else:
                    # No code part, skip the entire line
                    continue
            else:
                # Check if comment should be kept using the existing logic
                print(f"DEBUG: Processing comment: '{comment_part}'")
                if is_simple_comment(comment_part):
                    print(f"DEBUG: Keeping comment: '{comment_part}'")
                    # Keep the comment with EXACT original spacing
                    if code_part.strip():
                        # Preserve the exact original spacing before //
                        result_lines.append(line_content)
                    else:
                        result_lines.append(f"//{comment_part}")
                else:
                    # Remove the comment, keep only the code part with original spacing
                    print(f"DEBUG: Removing comment: {comment_part}")
                    if code_part.strip():
                        result_lines.append(code_part.rstrip())
                    else:
                        # Don't add empty lines - skip them completely
                        continue
        else:
            # No // comment in this line, keep it as is
            result_lines.append(line_content)
    
    # Join with single newlines, no extra spacing
    return '\n'.join(result_lines)

def clean_empty_lines(content):
    """Remove all empty lines while preserving indentation"""
    # Split content into lines, handling different line endings
    lines = content.splitlines()
    # Filter out empty lines but preserve indentation
    non_empty_lines = []
    for line in lines:
        # Check if line is empty (only whitespace)
        if line.strip():
            # Keep the line with its original indentation
            non_empty_lines.append(line)
    
    # Join with single newlines, no extra spacing
    return '\n'.join(non_empty_lines)

def remove_comments_smart_clean(content):
    """Clean version of remove_comments_smart without print statements for server use"""
    content = remove_html_comments(content)
    content = remove_multiline_comments(content)
    content = process_single_line_comments(content)
    content = clean_empty_lines(content)
    
    # Ensure no extra newlines at the end
    content = content.rstrip('\n')
    
    return content

def remove_comments_smart(content):
    """Main function to intelligently remove comments"""
    print("Removing HTML comments...")
    content = remove_html_comments(content)
    
    print("Removing multi-line comments...")
    content = remove_multiline_comments(content)
    
    print("Processing single-line comments intelligently...")
    content = process_single_line_comments(content)
    
    print("Cleaning up empty lines...")
    content = clean_empty_lines(content)
    
    return content

def create_web_server():
    """Create and configure the Flask web server"""
    app = Flask(__name__)
    CORS(app)
    
    # Force JSON responses for all errors
    app.config['PROPAGATE_EXCEPTIONS'] = True
    
    # Add error handlers to ensure JSON responses
    @app.errorhandler(400)
    def bad_request(error):
        print(f"DEBUG: 400 error handler called: {error}")
        return jsonify({'error': 'Bad request', 'details': str(error)}), 400
    
    @app.errorhandler(404)
    def not_found(error):
        print(f"DEBUG: 404 error handler called: {error}")
        return jsonify({'error': 'Not found', 'details': str(error)}), 404
    
    @app.errorhandler(405)
    def method_not_allowed(error):
        print(f"DEBUG: 405 error handler called: {error}")
        return jsonify({'error': 'Method not allowed', 'details': str(error)}), 405
    
    @app.errorhandler(500)
    def internal_error(error):
        print(f"DEBUG: 500 error handler called: {error}")
        return jsonify({'error': 'Internal server error', 'details': str(error)}), 500
    
    @app.errorhandler(Exception)
    def handle_exception(e):
        print(f"DEBUG: General exception handler called: {str(e)}")
        import traceback
        traceback.print_exc()
        return jsonify({'error': 'Unhandled exception', 'details': str(e)}), 500
    
    # Add before_request to log all requests
    @app.before_request
    def log_request_info():
        print(f"DEBUG: {request.method} {request.path} - Content-Type: {request.content_type}")
        if request.files:
            print(f"DEBUG: Files in request: {list(request.files.keys())}")
        if request.form:
            print(f"DEBUG: Form data: {list(request.form.keys())}")
    
    @app.route('/')
    def index():
        """Serve the main HTML page"""
        print("DEBUG: Serving index page")
        return HTML_TEMPLATE
    
    @app.route('/health')
    def health():
        """Health check endpoint"""
        print("DEBUG: Health check called")
        return jsonify({'status': 'healthy', 'message': 'Server is running'})
    
    @app.route('/test-json')
    def test_json():
        """Test JSON response"""
        print("DEBUG: Test JSON called")
        return jsonify({'test': True, 'message': 'JSON is working'})
    
    @app.route('/test-simple')
    def test_simple():
        """Test simple text response"""
        print("DEBUG: Test simple called")
        return "Simple text response - Flask is working"
    
    @app.route('/test-upload')
    def test_upload():
        """Test endpoint to verify Flask is working"""
        print("DEBUG: Test upload endpoint called")
        return jsonify({
            'status': 'ok',
            'message': 'Upload endpoint is accessible',
            'timestamp': str(datetime.datetime.now())
        })
    
    # Store processed files in memory for direct download
    processed_files = {}
    
    @app.route('/api/process-files', methods=['POST'])
    def process_files():
        """Process uploaded files and return cleaned versions"""
        print("DEBUG: ===== /api/process-files START =====")
        try:
            print("DEBUG: /api/process-files called")
            print(f"DEBUG: Request method: {request.method}")
            print(f"DEBUG: Request files: {list(request.files.keys())}")
            print(f"DEBUG: Request form: {list(request.form.keys())}")
            print(f"DEBUG: Content-Type: {request.content_type}")
            print(f"DEBUG: Content-Length: {request.content_length}")
            
            # Ensure this is a POST request
            if request.method != 'POST':
                print("DEBUG: Method not allowed")
                return jsonify({'error': 'Method not allowed'}), 405
            
            # Check if files are present
            if 'files' not in request.files:
                print("DEBUG: No files in request.files")
                return jsonify({'error': 'No files provided'}), 400
            
            files = request.files.getlist('files')
            print(f"DEBUG: Got {len(files)} files")
            
            # Safely get options with error handling
            try:
                options = json.loads(request.form.get('options', '{}'))
            except json.JSONDecodeError as e:
                print(f"DEBUG: JSON decode error for options: {e}")
                options = {}
            
            if not files:
                print("DEBUG: No files selected")
                return jsonify({'error': 'No files selected'}), 400
            
            results = []
            processed_files.clear()  # Clear previous files
            
            for i, file in enumerate(files):
                if file.filename == '':
                    print(f"DEBUG: Skipping empty filename at index {i}")
                    continue
                
                print(f"DEBUG: Processing file {i+1}/{len(files)}: {file.filename}")
                
                try:
                    # Read file content - use same method as CLI for consistency
                    file_content = file.read()
                    content = file_content.decode('utf-8', errors='ignore')
                    original_size = len(content)
                    
                    print(f"DEBUG: File {file.filename} - Original: {len(content)} chars, {len(content.split(chr(10)))} lines")
                    
                    cleaned_content = remove_comments_smart_clean(content)
                    cleaned_size = len(cleaned_content)
                    
                    print(f"DEBUG: File {file.filename} - Cleaned: {len(cleaned_content)} chars, {len(cleaned_content.split(chr(10)))} lines")
                    
                    # Keep original filename
                    cleaned_filename = secure_filename(file.filename)
                    
                    # Store cleaned content in memory instead of saving to disk
                    processed_files[cleaned_filename] = cleaned_content
                    
                    results.append({
                        'originalName': file.filename,
                        'cleanedName': cleaned_filename,
                        'originalSize': original_size,
                        'cleanedSize': cleaned_size
                    })
                    
                    print(f"DEBUG: Successfully processed {file.filename}")
                    
                except Exception as file_error:
                    print(f"DEBUG: Error processing file {file.filename}: {str(file_error)}")
                    import traceback
                    traceback.print_exc()
                    # Continue with other files instead of failing completely
                    continue
            
            # Create zip file in memory if multiple files were processed
            if len(results) > 1:
                try:
                    zip_filename = f"cleaned_files_{len(results)}_files.zip"
                    print(f"DEBUG: Creating zip file: {zip_filename}")
                    
                    # Create zip file in memory
                    zip_buffer = tempfile.NamedTemporaryFile(delete=False, suffix='.zip')
                    with zipfile.ZipFile(zip_buffer.name, 'w', zipfile.ZIP_DEFLATED) as zip_file:
                        for result in results:
                            cleaned_filename = result['cleanedName']
                            cleaned_content = processed_files[cleaned_filename]
                            zip_file.writestr(cleaned_filename, cleaned_content)
                    
                    # Store zip file path for download
                    processed_files[zip_filename] = zip_buffer.name
                    
                    # Add zip file to results
                    results.append({
                        'originalName': 'Multiple Files',
                        'cleanedName': zip_filename,
                        'originalSize': sum(r['originalSize'] for r in results[:-1]),
                        'cleanedSize': sum(r['cleanedSize'] for r in results[:-1]),
                        'isZip': True
                    })
                    
                    print(f"DEBUG: Successfully created zip file")
                    
                except Exception as zip_error:
                    print(f"DEBUG: Error creating zip file: {str(zip_error)}")
                    import traceback
                    traceback.print_exc()
                    # Continue without zip file
            
            if not results:
                print("DEBUG: No valid files processed")
                return jsonify({'error': 'No valid files processed'}), 400
            
            print(f"DEBUG: Returning success with {len(results)} results")
            response_data = {
                'success': True,
                'results': results,
                'message': f'Successfully processed {len(results)} file(s)'
            }
            print(f"DEBUG: Response data: {response_data}")
            print("DEBUG: ===== /api/process-files SUCCESS =====")
            return jsonify(response_data)
            
        except Exception as e:
            print(f"DEBUG: Unexpected error in process_files: {str(e)}")
            import traceback
            traceback.print_exc()
            error_response = {'error': f'Server error: {str(e)}'}
            print(f"DEBUG: Error response: {error_response}")
            print("DEBUG: ===== /api/process-files ERROR =====")
            return jsonify(error_response), 500
    
    @app.route('/api/download/<filename>')
    def download_file(filename):
        """Download a processed file from memory"""
        print(f"DEBUG: Download requested for: {filename}")
        try:
            if filename not in processed_files:
                print(f"DEBUG: File {filename} not found in processed_files")
                return jsonify({'error': 'File not found'}), 404
            
            file_content = processed_files[filename]
            print(f"DEBUG: File {filename} found, type: {type(file_content)}")
            
            # Check if it's a zip file (stored as file path) or regular file (stored as content)
            if filename.endswith('.zip'):
                # It's a zip file - file_content is actually the file path
                zip_path = file_content
                print(f"DEBUG: Serving zip file from path: {zip_path}")
                return send_file(zip_path, as_attachment=True, download_name=filename)
            else:
                # It's a regular file - file_content is the actual content
                print(f"DEBUG: Creating temp file for {filename}")
                # Create a temporary file to serve
                temp_file = tempfile.NamedTemporaryFile(mode='w', delete=False, suffix=os.path.splitext(filename)[1], encoding='utf-8')
                temp_file.write(file_content)
                temp_file.close()
                
                print(f"DEBUG: Serving file from temp path: {temp_file.name}")
                return send_file(temp_file.name, as_attachment=True, download_name=filename)
            
        except Exception as e:
            print(f"DEBUG: Error in download_file: {str(e)}")
            import traceback
            traceback.print_exc()
            return jsonify({'error': str(e)}), 500
    
    return app

def process_directory(input_dir, output_dir=None):
    """Process all supported files in a directory"""
    if not os.path.exists(input_dir):
        print(f"Error: Input directory '{input_dir}' not found.")
        return False
    
    if not os.path.isdir(input_dir):
        print(f"Error: '{input_dir}' is not a directory.")
        return False
    
    # Supported file extensions
    supported_extensions = {'.php', '.html', '.htm', '.js', '.css', '.txt'}
    
    # Create output directory if specified
    if output_dir:
        os.makedirs(output_dir, exist_ok=True)
        print(f"Output directory: {output_dir}")
    else:
        output_dir = input_dir
        print(f"Output directory: {input_dir} (same as input)")
    
    # Find all supported files
    files_to_process = []
    for root, dirs, files in os.walk(input_dir):
        for file in files:
            if any(file.endswith(ext) for ext in supported_extensions) and '_cleaned' not in file:
                file_path = os.path.join(root, file)
                files_to_process.append(file_path)
    
    if not files_to_process:
        print(f"No supported files found in '{input_dir}'")
        return False
    
    print(f"Found {len(files_to_process)} files to process:")
    for file_path in files_to_process:
        print(f"  - {file_path}")
    
    # Process each file
    processed_count = 0
    total_original_size = 0
    total_cleaned_size = 0
    
    for file_path in files_to_process:
        try:
            # Get just the filename without directory path
            filename = os.path.basename(file_path)
            if output_dir == input_dir:
                # Same directory - overwrite original file
                output_file = file_path
            else:
                # Different output directory - use just filename (no directory structure)
                output_file = os.path.join(output_dir, filename)
            
            # Create output directory if needed
            os.makedirs(output_dir, exist_ok=True)
            
            print(f"\nProcessing: {file_path}")
            with open(file_path, 'r', encoding='utf-8') as f:
                content = f.read()
            
            original_size = len(content)
            total_original_size += original_size
            
            # Remove comments intelligently
            cleaned_content = remove_comments_smart_clean(content)
            cleaned_size = len(cleaned_content)
            total_cleaned_size += cleaned_size
            
            # Write output file
            with open(output_file, 'w', encoding='utf-8', newline='') as f:
                f.write(cleaned_content)
            
            if output_file == file_path:
                print(f"  → Updated: {output_file}")
            else:
                print(f"  → {output_file}")
            print(f"  Size: {original_size} → {cleaned_size} bytes (removed {original_size - cleaned_size})")
            
            processed_count += 1
            
        except Exception as e:
            print(f"  Error processing {file_path}: {e}")
    
    print(f"\n✅ Directory processing completed!")
    print(f"Processed {processed_count}/{len(files_to_process)} files")
    print(f"Total size: {total_original_size} → {total_cleaned_size} bytes")
    print(f"Total removed: {total_original_size - total_cleaned_size} bytes")
    
    return True

def main():
    # Check if web mode is requested
    if len(sys.argv) > 1 and sys.argv[1] == '--web':
        print("🚀 Starting Smart Comment Remover Web Server...")
        print("🌐 Server will be available at: http://localhost:5000")
        print("📖 Open your browser and navigate to the URL above")
        print("📂 Supports both individual files and entire directories")
        print("💾 Files processed in memory - direct download, no disk storage")
        print("\nPress Ctrl+C to stop the server")
        
        try:
            print("DEBUG: Creating Flask app...")
            app = create_web_server()
            print("DEBUG: Flask app created successfully")
            
            # Get port from environment variable (for Render) or use 5000 for local development
            port = int(os.environ.get('PORT', 5000))
            print(f"DEBUG: Starting Flask app on port {port}")
            print(f"DEBUG: Environment: {os.environ.get('RENDER', 'local')}")
            
            # Configure for production but ensure JSON responses
            app.config['ENV'] = 'production'
            app.config['DEBUG'] = False
            app.config['TESTING'] = False
            app.config['PROPAGATE_EXCEPTIONS'] = True
            
            # Add global exception handler
            @app.errorhandler(Exception)
            def handle_all_exceptions(e):
                print(f"DEBUG: Global exception handler: {str(e)}")
                import traceback
                traceback.print_exc()
                return jsonify({'error': 'Server error', 'details': str(e)}), 500
            
            print("DEBUG: Starting Flask server...")
            app.run(host='0.0.0.0', port=port, debug=False, use_reloader=False)
            
        except Exception as e:
            print(f"DEBUG: Error starting Flask app: {str(e)}")
            import traceback
            traceback.print_exc()
            sys.exit(1)
        
        return
    
    # CLI mode
    if len(sys.argv) < 2:
        print("Usage:")
        print("  CLI mode:")
        print("    Single file: python remove_comments_smart.py input_file.php [output_file.php]")
        print("    Directory:   python remove_comments_smart.py --dir input_directory [output_directory]")
        print("  Web mode: python remove_comments_smart.py --web")
        print("\nIf output file/directory is not specified, will overwrite original files")
        sys.exit(1)
    
    # Check if directory mode is requested
    if sys.argv[1] == '--dir':
        if len(sys.argv) < 3:
            print("Error: Directory mode requires input directory path")
            print("Usage: python remove_comments_smart.py --dir input_directory [output_directory]")
            sys.exit(1)
        
        input_dir = sys.argv[2]
        output_dir = sys.argv[3] if len(sys.argv) >= 4 else None
        
        process_directory(input_dir, output_dir)
        return
    
    # Single file mode
    input_file = sys.argv[1]
    
    if not os.path.exists(input_file):
        print(f"Error: Input file '{input_file}' not found.")
        sys.exit(1)
    
    # Determine output filename
    if len(sys.argv) >= 3:
        output_file = sys.argv[2]
    else:
        # Overwrite original file
        output_file = input_file
    
    try:
        print(f"Reading input file: {input_file}")
        with open(input_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        print(f"Original file size: {len(content)} characters")
        print(f"Original file lines: {len(content.split(chr(10)))}")
        
        # Remove comments intelligently
        cleaned_content = remove_comments_smart(content)
        
        print(f"Cleaned file size: {len(cleaned_content)} characters")
        print(f"Cleaned file lines: {len(cleaned_content.split(chr(10)))}")
        print(f"Removed {len(content) - len(cleaned_content)} characters")
        
        # Write output file
        print(f"Writing output file: {output_file}")
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(cleaned_content)
        
        if output_file == input_file:
            print(f"Successfully updated: {output_file}")
        else:
            print(f"Successfully created: {output_file}")
        print("\nSmart comment removal completed!")
        print("- Kept: Simple descriptive comments")
        print("- Removed: Comments with code characters, HTML comments, multi-line comments")
        
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main() 