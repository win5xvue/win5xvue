#!/bin/bash
rm nohup.out
nohup ./geth --ws --wsport 9546 --wsorigins 127.0.0.1 --rpc --syncmode "light" --rpcaddr "0.0.0.0" --rpcapi "admin,personal,eth,net,web3" --wsapi "admin,personal,eth,net,web3" --allow-insecure-unlock & disown
