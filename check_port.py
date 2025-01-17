import requests
import urllib.parse
from urllib.parse import urlparse
import socket

def check_port(url):
    try:
        # URL encode the path properly
        encoded_url = urllib.parse.quote(url, safe=':/?&=')
        
        # Parse the URL to get components
        parsed = urlparse(encoded_url)
        
        # If no scheme specified, add http://
        if not parsed.scheme:
            encoded_url = 'http://' + encoded_url
            parsed = urlparse(encoded_url)
        
        # Get hostname and path
        hostname = parsed.hostname
        path = parsed.path
        
        # Try common HTTP ports
        common_ports = [80, 8080, 443, 8000, 8888]
        
        print(f"Checking ports for {hostname}{path}...")
        
        for port in common_ports:
            sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
            sock.settimeout(1)  # Set timeout to 1 second
            
            try:
                # Try to connect to the port
                result = sock.connect_ex((hostname, port))
                
                if result == 0:
                    # Try to make an HTTP request
                    try:
                        if port == 443:
                            protocol = 'https'
                        else:
                            protocol = 'http'
                            
                        test_url = f"{protocol}://{hostname}:{port}{path}"
                        response = requests.get(test_url, timeout=2)
                        
                        if response.status_code == 200:
                            print(f"\nSuccess! The application is running on:")
                            print(f"Port: {port}")
                            print(f"Full URL: {test_url}")
                            return port
                            
                    except requests.RequestException:
                        continue
                        
            except socket.error:
                print(f"Could not connect to port {port}")
                
            finally:
                sock.close()
                
        print("\nNo active HTTP server found on common ports.")
        return None
        
    except Exception as e:
        print(f"Error: {str(e)}")
        return None

if __name__ == "__main__":
    # Test URL
    url = "localhost/birth & death beta 3/index.php"
    port = check_port(url)
    
    if port:
        print("\nYou can access your application at:")
        print(f"http://localhost:{port}/birth & death beta 3/index.php") 