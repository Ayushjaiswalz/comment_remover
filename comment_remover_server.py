#!/usr/bin/env python3
"""
Comment Remover Web Server

This Flask server provides a web API for the comment removal tool.
It handles file uploads and processes them using the comment removal scripts.
"""

from flask import Flask, request, jsonify, send_file
from flask_cors import CORS
import os
import tempfile
import zipfile
from werkzeug.utils import secure_filename
import json
from remove_comments_smart import remove_comments_smart

app = Flask(__name__)
CORS(app)  # Enable CORS for all routes

# Configuration
UPLOAD_FOLDER = 'uploads'
PROCESSED_FOLDER = 'processed'
MAX_CONTENT_LENGTH = 50 * 1024 * 1024  # 50MB max file size

# Create directories if they don't exist
os.makedirs(UPLOAD_FOLDER, exist_ok=True)
os.makedirs(PROCESSED_FOLDER, exist_ok=True)

# Allowed file extensions
ALLOWED_EXTENSIONS = {'.php', '.html', '.htm', '.js', '.css', '.txt'}

def allowed_file(filename):
    """Check if file extension is allowed"""
    return '.' in filename and \
           filename.rsplit('.', 1)[1].lower() in [ext[1:] for ext in ALLOWED_EXTENSIONS]

def get_file_extension(filename):
    """Get file extension with dot"""
    return '.' + filename.rsplit('.', 1)[1].lower()

def process_file_content(content, options):
    """Process file content based on selected options"""
    if options.get('removeHtmlComments', True):
        # Remove HTML comments
        import re
        content = re.sub(r'<!--.*?-->', '', content, flags=re.DOTALL)
    
    if options.get('removeMultilineComments', True):
        # Remove multi-line comments
        import re
        content = re.sub(r'/\*.*?\*/', '', content, flags=re.DOTALL)
    
    if options.get('removeCodeComments', True):
        # Remove comments with code characters
        lines = content.split('\n')
        result_lines = []
        
        for line in lines:
            if '//' in line:
                parts = line.split('//', 1)
                code_part = parts[0].rstrip()
                comment_part = parts[1] if len(parts) > 1 else ""
                
                # Check if comment should be kept (simple descriptive)
                if is_simple_comment(comment_part):
                    if code_part.strip():
                        result_lines.append(f"{code_part} //{comment_part}")
                    else:
                        result_lines.append(f"//{comment_part}")
                else:
                    # Remove the comment, keep only the code part
                    if code_part.strip():
                        result_lines.append(code_part)
            else:
                result_lines.append(line)
        
        content = '\n'.join(result_lines)
    
    if options.get('cleanEmptyLines', True):
        # Clean up excessive empty lines
        import re
        content = re.sub(r'\n\s*\n\s*\n', '\n\n', content)
        content = content.strip()
    
    return content

def is_simple_comment(comment_text):
    """Check if a comment is simple and descriptive (should be kept)"""
    clean_text = comment_text.strip()
    
    if not clean_text or len(clean_text) < 3:
        return False
    
    # Check if comment contains code-related characters
    code_chars = ['<', '>', '(', ')', '[', ']', '{', '}', '=', '+', '-', '*', '/', '\\', '|', '&', '^', '%', '$', '@', '!', '?', ';', ':', '"', "'", '`']
    
    for char in code_chars:
        if char in clean_text:
            return False
    
    # If comment is just repeated characters or separators, remove it
    import re
    if re.match(r'^[=\-\*_]+$', clean_text):
        return False
    
    # If comment looks like a section header with separators, remove it
    if re.match(r'^[=\-\*_]+.*[=\-\*_]+$', clean_text):
        return False
    
    return True

@app.route('/api/process-files', methods=['POST'])
def process_files():
    """Process uploaded files and return cleaned versions"""
    try:
        if 'files' not in request.files:
            return jsonify({'error': 'No files provided'}), 400
        
        files = request.files.getlist('files')
        options = json.loads(request.form.get('options', '{}'))
        
        if not files:
            return jsonify({'error': 'No files selected'}), 400
        
        results = []
        processed_files = []
        
        for file in files:
            if file.filename == '':
                continue
                
            if not allowed_file(file.filename):
                continue
            
            # Read file content
            content = file.read().decode('utf-8', errors='ignore')
            original_size = len(content)
            
            # Process the content
            cleaned_content = process_file_content(content, options)
            cleaned_size = len(cleaned_content)
            
            # Create cleaned filename
            name, ext = os.path.splitext(secure_filename(file.filename))
            cleaned_filename = f"{name}_cleaned{ext}"
            
            # Save cleaned file
            cleaned_file_path = os.path.join(PROCESSED_FOLDER, cleaned_filename)
            with open(cleaned_file_path, 'w', encoding='utf-8') as f:
                f.write(cleaned_content)
            
            results.append({
                'originalName': file.filename,
                'cleanedName': cleaned_filename,
                'originalSize': original_size,
                'cleanedSize': cleaned_size,
                'filePath': cleaned_file_path
            })
            
            processed_files.append(cleaned_file_path)
        
        if not results:
            return jsonify({'error': 'No valid files processed'}), 400
        
        # Create zip file if multiple files
        if len(results) > 1:
            zip_path = os.path.join(PROCESSED_FOLDER, 'cleaned_files.zip')
            with zipfile.ZipFile(zip_path, 'w') as zipf:
                for file_path in processed_files:
                    zipf.write(file_path, os.path.basename(file_path))
            
            # Add zip file to results
            results.append({
                'originalName': 'Multiple Files',
                'cleanedName': 'cleaned_files.zip',
                'originalSize': sum(r['originalSize'] for r in results[:-1]),
                'cleanedSize': os.path.getsize(zip_path),
                'filePath': zip_path,
                'isZip': True
            })
        
        return jsonify({
            'success': True,
            'results': results,
            'message': f'Successfully processed {len(results)} file(s)'
        })
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/download/<filename>')
def download_file(filename):
    """Download a processed file"""
    try:
        file_path = os.path.join(PROCESSED_FOLDER, filename)
        if not os.path.exists(file_path):
            return jsonify({'error': 'File not found'}), 404
        
        return send_file(file_path, as_attachment=True, download_name=filename)
        
    except Exception as e:
        return jsonify({'error': str(e)}), 500

@app.route('/api/health')
def health_check():
    """Health check endpoint"""
    return jsonify({'status': 'healthy', 'message': 'Comment Remover Server is running'})

@app.route('/')
def index():
    """Serve the main HTML page"""
    return send_file('comment_remover_ui.html')

if __name__ == '__main__':
    print("🚀 Starting Comment Remover Web Server...")
    print("📁 Upload folder:", os.path.abspath(UPLOAD_FOLDER))
    print("📁 Processed folder:", os.path.abspath(PROCESSED_FOLDER))
    print("🌐 Server will be available at: http://localhost:5000")
    print("📖 Open your browser and navigate to the URL above")
    print("\nPress Ctrl+C to stop the server")
    
    app.run(host='0.0.0.0', port=5000, debug=True) 