#!/usr/bin/env python3
"""
Smart PHP Comment Remover Script

This script intelligently removes comments from PHP files:
- KEEPS: Simple descriptive comments like "//fetch ai apprenticeship within district"
- REMOVES: Comments with code-related characters like <>()[]{} etc.
- REMOVES: HTML comments <!-- -->
- REMOVES: Multi-line comments /* */

Usage: python remove_comments_smart.py input_file.php [output_file.php]
"""

import re
import sys
import os

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

def remove_html_comments(content):
    """Remove HTML comments <!-- -->"""
    return re.sub(r'<!--.*?-->', '', content, flags=re.DOTALL)

def remove_multiline_comments(content):
    """Remove multi-line comments /* */"""
    return re.sub(r'/\*.*?\*/', '', content, flags=re.DOTALL)

def process_single_line_comments(content):
    """Process single-line comments intelligently"""
    lines = content.split('\n')
    result_lines = []
    
    for line in lines:
        # Check for // comments
        if '//' in line:
            # Split the line at //
            parts = line.split('//', 1)
            code_part = parts[0].rstrip()
            comment_part = parts[1] if len(parts) > 1 else ""
            
            # Check if this is a URL or special case
            if any(url_indicator in line for url_indicator in ['http://', 'https://', 'ftp://', 'file://']):
                # This is a URL, keep the line as is
                result_lines.append(line)
                continue
            
            # Check if comment should be kept
            if is_simple_comment(comment_part):
                # Keep the comment, just clean up the line
                if code_part.strip():
                    result_lines.append(f"{code_part} //{comment_part}")
                else:
                    result_lines.append(f"//{comment_part}")
            else:
                # Remove the comment, keep only the code part
                if code_part.strip():
                    result_lines.append(code_part)
        else:
            # No // comment in this line, keep it as is
            result_lines.append(line)
    
    return '\n'.join(result_lines)

def clean_empty_lines(content):
    """Clean up excessive empty lines"""
    # Replace multiple consecutive empty lines with single empty line
    content = re.sub(r'\n\s*\n\s*\n', '\n\n', content)
    # Remove empty lines at the beginning and end
    content = content.strip()
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

def main():
    if len(sys.argv) < 2:
        print("Usage: python remove_comments_smart.py input_file.php [output_file.php]")
        print("If output file is not specified, will create input_file_smart_cleaned.php")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    if not os.path.exists(input_file):
        print(f"Error: Input file '{input_file}' not found.")
        sys.exit(1)
    
    # Determine output filename
    if len(sys.argv) >= 3:
        output_file = sys.argv[2]
    else:
        name, ext = os.path.splitext(input_file)
        output_file = f"{name}_smart_cleaned{ext}"
    
    try:
        print(f"Reading input file: {input_file}")
        with open(input_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        print(f"Original file size: {len(content)} characters")
        
        # Remove comments intelligently
        cleaned_content = remove_comments_smart(content)
        
        print(f"Cleaned file size: {len(cleaned_content)} characters")
        print(f"Removed {len(content) - len(cleaned_content)} characters")
        
        # Write output file
        print(f"Writing output file: {output_file}")
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(cleaned_content)
        
        print(f"Successfully created: {output_file}")
        print("\nSmart comment removal completed!")
        print("- Kept: Simple descriptive comments")
        print("- Removed: Comments with code characters, HTML comments, multi-line comments")
        
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main() 