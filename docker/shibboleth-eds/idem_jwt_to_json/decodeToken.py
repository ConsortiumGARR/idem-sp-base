#!/usr/bin/env python3

import jwt
import json
import pem
import sys
import getopt
import requests
import logging

# Imposta logger su stdout
logging.basicConfig(
    level=logging.INFO,
    format='[jwt2json] [%(asctime)s] [%(levelname)s] %(message)s',
    handlers=[logging.StreamHandler(sys.stdout)]
)

def main(argv):
    try:
        opts, args = getopt.getopt(argv, 'j:o:k:hd', ['jwt=', 'output=', 'publickey=', 'help', 'debug'])
    except getopt.GetoptError as err:
        logging.error(f"Argument error: {err}")
        logging.info("Usage: ./decodeToken.py -j <jwt_inputurl> -o <output_path> -k <publickey_path>")
        sys.exit(2)

    inputurl = outputpath = publickey = None

    for opt, arg in opts:
        if opt in ('-h', '--help'):
            logging.info("Usage: ./decodeToken.py -j <jwt_inputurl> -o <output_path> -k <publickey_path>")
            sys.exit()
        elif opt in ('-j', '--jwt'):
            inputurl = arg
        elif opt in ('-o', '--output'):
            outputpath = arg
        elif opt in ('-k', '--publickey'):
            publickey = arg
        elif opt == '-d':
            logging.getLogger().setLevel(logging.DEBUG)

    if not inputurl or not outputpath or not publickey:
        logging.error("Missing required arguments!")
        logging.info("Usage: ./decodeToken.py -j <jwt_inputurl> -o <output_path> -k <publickey_path>")
        sys.exit(1)

    try:
        logging.info(f"Reading public key from: {publickey}")
        with open(publickey, 'r') as rsa_pubkey:
            pubkey = rsa_pubkey.read()

        logging.info(f"Fetching JWT from: {inputurl}")
        jwt_token = requests.get(inputurl, allow_redirects=True)
        token = jwt_token.content

        logging.info("Decoding JWT")
        decode = jwt.decode(token, pubkey, algorithms=["RS256"])
        json_data = decode.get("data", {})

        json_decoded = json.dumps(json_data, indent=4, ensure_ascii=False)

        logging.info(f"Writing output to: {outputpath}")
        with open(outputpath, "w", encoding="utf-8") as f:
            f.write(json_decoded)

        logging.info("Done. No problem occurred.")

    except Exception as e:
        logging.exception(f"An error occurred: {e}")
        sys.exit(1)

if __name__ == "__main__":
    main(sys.argv[1:])
