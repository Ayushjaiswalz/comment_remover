import re
import sys
import os

def remove_comments(content):
    content = re.sub(r'<!--.*?-->', '', content, flags=re.DOTALL)
    content = re.sub(r'/\*.*?\*/', '', content, flags=re.DOTALL)
    
    lines = content.split('\n')
    result_lines = []
    
    for line in lines:
        processed_line = re.sub(r'(?<!:)(?<!http)(?<!https)(?<!ftp)(?<!file)(?<!//)(?<!\\\\)//.*$', '', line)
        processed_line = re.sub(r'#.*$', '', processed_line)
        processed_line = processed_line.rstrip()
        if processed_line.strip():
            result_lines.append(processed_line)

    result = '\n'.join(result_lines)
    result = re.sub(r'\n\s*\n\s*\n', '\n\n', result)
    result = result.strip()
    
    return result

def main():
    if len(sys.argv) < 2:
        print("Usage: python remove_comments_simple.py input_file.php [output_file.php]")
        print("If output file is not specified, will create input_file_no_comments.php")
        sys.exit(1)
    
    input_file = sys.argv[1]
    
    if not os.path.exists(input_file):
        print(f"Error: Input file '{input_file}' not found.")
        sys.exit(1)

    if len(sys.argv) >= 3:
        output_file = sys.argv[2]
    else:
        name, ext = os.path.splitext(input_file)
        output_file = f"{name}_no_comments{ext}"
    
    try:
        print(f"Reading input file: {input_file}")
        with open(input_file, 'r', encoding='utf-8') as f:
            content = f.read()
        
        print(f"Original file size: {len(content)} characters")
        
        
        cleaned_content = remove_comments(content)
        
        print(f"Cleaned file size: {len(cleaned_content)} characters")
        print(f"Removed {len(content) - len(cleaned_content)} characters")
        
        print(f"Writing output file: {output_file}")
        with open(output_file, 'w', encoding='utf-8') as f:
            f.write(cleaned_content)
        
        print(f"Successfully created: {output_file}")
        
    except Exception as e:
        print(f"Error: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main()