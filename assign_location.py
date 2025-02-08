import requests

url = 'http://127.0.0.1:8000/api/receive-data'  # Correct URL

# Data to send (can be a dictionary, list, etc.)
data = {
    'id':'M1',  # Example product ID
    'zone_name':'Dry Zone'
}
headers = {'Content-Type': 'application/json'}

# Send POST request
response = requests.post(url, json=data)

# Check the response
print(response.status_code)  # Status Code
print(response.json()) 
