<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <!-- PAYPAL SDK -->
        <script src="https://www.paypal.com/sdk/js?client-id={{ config('paypal.client_id') }}&vault=true&intent=subscription" data-sdk-integration-source="buttons"></script>
        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    </head>
    <body class="antialiased">
        <div id="paypal-button-container"></div>
    </body>
    <script>
        paypal.Buttons({
            fundingSource: paypal.FUNDING.PAYPAL,
            disableS: false,
            createSubscription: function (data, actions) {
                return axios.post('/api/paypal/subscription/create/')
                    .then(function(orderData) {
                        console.log(orderData);
                        return orderData.data.id;
                    });
            },
            onApprove: function (data, actions) {
                return axios.post('/api/paypal/subscription/' + data.subscriptionID + '/capture/')
                    .then(function(orderData) {
                        console.log(orderData.data);
                        alert(orderData.data.status + " Subscription plan\n" + 'Transaction completed by ' + orderData.data.subscriber.name.given_name);
                    });
            }
        }).render('#paypal-button-container');
    </script>
</html>
