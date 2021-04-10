const WebSocket = require('ws'), axios = require('axios'),
    { r, g, b, w, c, m, y, k } = [['r', 1], ['g', 2], ['b', 4], ['w', 7], ['c', 6], ['m', 5], ['y', 3], ['k', 0]].reduce((cols, col) => ({ ...cols,  [col[0]]: f => `\x1b[3${col[1]}m${f}\x1b[0m` }), {});

let socket = null, connectionInterval = null, latestData = null;

const connect = () => {
    if(socket) socket.close();
    if(connectionInterval) clearInterval(connectionInterval);

    socket = new WebSocket('wss://ws-feed.pro.coinbase.com');

    socket.addEventListener('open', function (event) {
        socket.send(JSON.stringify({
            type: "subscribe",
            channels: [
                {
                    name: "ticker",
                    product_ids: [
                        "BTC-USD",
                        "ETH-USD"
                    ]
                }
            ]
        }));
    });

    socket.addEventListener('message', function (message) {
        const data = JSON.parse(message.data);

        latestData = data;

        if (data.type === 'ticker') {
            console.log(r(data.product_id), w(JSON.stringify(data)));

            axios.post('http://localhost/api/node/pushBullData', {
                data: {
                    [data.product_id]: data
                }
            }).catch((error) => console.error('Failed to push data!'));
        } else console.log(c('WS Message'), message.data);
    });

    let latestDataTested = null;
    connectionInterval = setInterval(() => {
        if(latestData && latestDataTested && latestData === latestDataTested) connect();
        latestDataTested = latestData;
    }, 10000);
};

connect();
