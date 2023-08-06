from http.server import BaseHTTPRequestHandler, HTTPServer
from urllib.parse import parse_qs
import cgi, json
import tiktoken
import time

class RequestHandler(BaseHTTPRequestHandler):
    def _send_response(self, message):
        self.send_response(200)
        self.send_header('Content-type', 'text/html')
        self.end_headers()
        self.wfile.write(bytes(message, "utf8"))

    def num_tokens_from_messages(self, messages, model="gpt-3.5-turbo-0613"):
        """Return the number of tokens used by a list of messages."""
        try:
            encoding = tiktoken.encoding_for_model(model)
        except KeyError:
            print("Warning: model not found. Using gpt-3.5-turbo-0613 encoding.")
            encoding = tiktoken.get_encoding("gpt-3.5-turbo-0613")
        if model in {
            "gpt-3.5-turbo-0613",
            "gpt-3.5-turbo-16k-0613",
            "gpt-4-0314",
            "gpt-4-32k-0314",
            "gpt-4-0613",
            "gpt-4-32k-0613",
            }:
            tokens_per_message = 3
            tokens_per_name = 1
        elif model == "gpt-3.5-turbo-0301":
            tokens_per_message = 4  # every message follows <|start|>{role/name}\n{content}<|end|>\n
            tokens_per_name = -1  # if there's a name, the role is omitted
        elif "gpt-3.5-turbo" in model:
            return num_tokens_from_messages(messages, model="gpt-3.5-turbo-0613")
        elif "gpt-4" in model:
            return num_tokens_from_messages(messages, model="gpt-4-0613")
        else:
            #raise NotImplementedError(
            #    f"""num_tokens_from_messages() is not implemented for model {model}. See https://github.com/openai/openai-python/blob/main/chatml.md for information on how messages are converted to tokens."""
            #)
            return 0 # return 0 on error condition, better then breaking the app
        num_tokens = 0
        for message in messages:
            num_tokens += tokens_per_message
            for key, value in message.items():
                # ignore non-str values (like 'function_call' which has dict as value)
                if not isinstance(value, str):
                    continue
                num_tokens += len(encoding.encode(value))
                if key == "name":
                    num_tokens += tokens_per_name
        num_tokens += 3  # every reply is primed with <|start|>assistant<|message|>
        return num_tokens

    def do_POST(self):
        start_time = time.perf_counter()
        ctype, pdict = cgi.parse_header(self.headers.get('content-type'))
        if ctype == 'application/json':
            length = int(self.headers.get('content-length'))
            postvars = json.loads(self.rfile.read(length))
            #print(postvars)
            #start_time = time.perf_counter()
            tok_num = self.num_tokens_from_messages(postvars['messages'], postvars['model'])
            self._send_response(str(tok_num))
            end_time = time.perf_counter()
            execution_time = (end_time - start_time) * 1000  # Convert to milliseconds
            with open('tokenizer_log_file.txt', 'a') as file: # this will end up in /tmp
                file.write(f"The code executed in {execution_time}ms\n")
                file.write(json.dumps(postvars))
                file.write('\n')
                #print(f"The code executed in {execution_time}ms")

        else:
            self.send_response(415)
            self.end_headers()
            self.wfile.write(bytes('Unsupported Media Type', 'utf-8'))

port = 8090
httpd = HTTPServer(('localhost', port), RequestHandler)
print(f"Starting server on port {port}...")
httpd.serve_forever()
