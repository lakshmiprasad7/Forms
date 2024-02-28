import re
from flask import Flask, render_template, request, jsonify
from PIL import Image
import pytesseract

app = Flask(__name__, template_folder='templatess')

def extract_aadhar_info(text):
    # Find potential Aadhar number candidates
    aadhar_candidates = re.findall(r'\b\d{4}[\s-]?\d{4}[\s-]?\d{4}\b', text)

    # Filter and validate candidates
    valid_aadhar_numbers = [candidate for candidate in aadhar_candidates if is_valid_aadhar(candidate)]

    # Choose the first valid Aadhar number (if any)
    aadhar_number = valid_aadhar_numbers[0] if valid_aadhar_numbers else None

    # Extract address using a more lenient pattern
    address_match = re.search(r'\b(Address|ADDR)[:\s]*(.+?)(?=\b\d{4}[\s-]?\d{4}[\s-]?\d{4}\b|$)', text, re.IGNORECASE)
    address = address_match.group(2).strip() if address_match else None

    # Extract name using a more lenient pattern
    name_match = re.search(r'\b(Name)[:\s]*(.+?)(?=\b\d{4}[\s-]?\d{4}[\s-]?\d{4}\b|$)', text, re.IGNORECASE)
    name = name_match.group(2).strip() if name_match else None

    return aadhar_number, name, address

def is_valid_aadhar(aadhar):
    # Add any additional validation logic for Aadhar numbers
    # For example, you can check the checksum or other rules
    return True  # Placeholder, replace with actual validation

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/upload', methods=['POST'])
def upload_file():
    if 'file' not in request.files:
        return jsonify({'error': 'No file part'})

    file = request.files['file']

    if file.filename == '':
        return jsonify({'error': 'No selected file'})

    try:
        # Configure Pytesseract to enhance OCR performance
        custom_config = r'--oem 3 --psm 6'
        image = Image.open(file)
        text = pytesseract.image_to_string(image, config=custom_config)

        # Extract Aadhar number, name, and address from 'text'
        aadhar_number, name, address = extract_aadhar_info(text)

        return jsonify({'aadharNumber': aadhar_number, 'name': name, 'address': address})
    except Exception as e:
        return jsonify({'error': str(e)})

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0')

