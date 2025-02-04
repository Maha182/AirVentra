import requests

url = 'http://127.0.0.1:8000/api/receive-data'  # Correct URL

# Data to send (can be a dictionary, list, etc.)
data = {
    'key1': 'value1',
    'key2': 'value2'
}

# Send POST request
response = requests.post(url, json=data)

# Check the response
print(response.status_code)  # Status Code
print(response.json()) 
