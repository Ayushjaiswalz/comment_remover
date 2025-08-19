# Comment Remover Web Application Setup

This guide will help you set up and run the Comment Remover web application with a beautiful UI.

## Prerequisites

- Python 3.7 or higher
- pip (Python package installer)

## Installation

### 1. Install Python Dependencies

```bash
pip install -r requirements.txt
```

Or install manually:
```bash
pip install Flask Flask-CORS Werkzeug
```

### 2. Verify Files

Make sure you have these files in your directory:
- `comment_remover_server.py` - Python backend server
- `comment_remover_ui.html` - Web UI
- `remove_comments_smart.py` - Comment removal logic
- `requirements.txt` - Python dependencies

## Running the Application

### 1. Start the Python Server

```bash
python comment_remover_server.py
```

You should see output like:
```
🚀 Starting Comment Remover Web Server...
📁 Upload folder: /path/to/your/project/uploads
📁 Processed folder: /path/to/your/project/processed
🌐 Server will be available at: http://localhost:5000
📖 Open your browser and navigate to the URL above

Press Ctrl+C to stop the server
```

### 2. Open the Web Interface

Open your web browser and navigate to:
```
http://localhost:5000
```

## Features

### Web Interface
- **Drag & Drop**: Simply drag files or folders onto the interface
- **File Selection**: Click buttons to browse and select files
- **Directory Support**: Select entire directories for batch processing
- **Real-time Progress**: See processing progress with visual feedback
- **Download Results**: Download cleaned files individually or as a ZIP

### Processing Options
- ✅ **Keep Simple Comments**: Preserves useful descriptive comments
- ❌ **Remove HTML Comments**: Removes `<!-- -->` blocks
- ❌ **Remove Multi-line Comments**: Removes `/* */` blocks
- ❌ **Remove Code Comments**: Removes comments with code characters
- 🧹 **Clean Empty Lines**: Removes excessive whitespace

### Supported File Types
- PHP files (.php)
- HTML files (.html, .htm)
- JavaScript files (.js)
- CSS files (.css)
- Text files (.txt)

## Usage

### 1. Select Files
- Drag and drop files onto the interface
- Or click "Select Files" to browse
- Or click "Select Directory" for batch processing

### 2. Configure Options
- Check/uncheck processing options as needed
- All options are enabled by default (recommended)

### 3. Process Files
- Click "Process Files" button
- Watch the progress bar
- Wait for completion

### 4. Download Results
- Download individual cleaned files
- Or download all files as a ZIP archive
- Files are saved with `_cleaned` suffix

## File Structure

After running, the application creates:
```
your_project/
├── uploads/           # Temporary upload storage
├── processed/         # Cleaned output files
├── comment_remover_server.py
├── comment_remover_ui.html
├── remove_comments_smart.py
└── requirements.txt
```

## Troubleshooting

### Server Won't Start
- Check if port 5000 is already in use
- Verify Python and pip are installed correctly
- Run `pip install -r requirements.txt` again

### Can't Connect to Server
- Make sure the Python server is running
- Check browser console for errors
- Verify the server is running on http://localhost:5000

### File Processing Errors
- Check file permissions
- Ensure files are valid text files
- Check server logs for detailed error messages

### Browser Issues
- Try a different browser
- Clear browser cache
- Check if JavaScript is enabled

## Stopping the Server

Press `Ctrl+C` in the terminal where the server is running.

## Advanced Usage

### Custom Port
Edit `comment_remover_server.py` and change:
```python
app.run(host='0.0.0.0', port=5000, debug=True)
```

### Custom File Types
Edit `ALLOWED_EXTENSIONS` in the server file to add more file types.

### Production Deployment
For production use, consider:
- Using a production WSGI server like Gunicorn
- Setting `debug=False`
- Adding authentication
- Using environment variables for configuration

## Support

If you encounter issues:
1. Check the server console for error messages
2. Verify all files are in the correct location
3. Ensure Python dependencies are installed
4. Check file permissions and network settings 