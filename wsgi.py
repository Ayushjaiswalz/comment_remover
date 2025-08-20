#!/usr/bin/env python3
"""
WSGI entry point for Render deployment
This file is used by gunicorn to serve the Flask application
"""

import os
import sys

# Add the current directory to Python path
sys.path.insert(0, os.path.dirname(__file__))

# Import the Flask app from our main script
from remove_comments_smart import create_web_server

# Create the Flask application
app = create_web_server()

if __name__ == "__main__":
    # This should not be called directly on Render
    # Render will use gunicorn with this file
    print("WSGI file loaded - use gunicorn to run this")
    app.run()
