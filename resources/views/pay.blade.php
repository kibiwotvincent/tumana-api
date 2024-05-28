<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe One-Off Charge</title>
    <script src="https://js.stripe.com/v3/"></script>
    <style>
        /* Add some basic styling */
        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Make a Payment</h2>
        <form id="payment-form">
            <div class="form-group">
                <label for="card-element">Credit or debit card</label>
                <div id="card-element"></div>
                <div id="card-errors" role="alert"></div>
            </div>
            <button type="submit">Submit Payment</button>
        </form>
    </div>

    <script>
        // Your Stripe publishable key
        $publishableKey = 'pk_test_51PKYxrBm2kKWxbxfILSEzmYYplxLnc7kUyvozPce9AzqUoLBLO81kdNITF9IiO8TJieHZrElqOOAJzfGm50HWThp00Io5x3gE9';
        const stripe = Stripe($publishableKey);
        const elements = stripe.elements();

        // Set up Stripe.js and Elements to use in checkout form
        const style = {
            base: {
                color: '#32325d',
                fontFamily: 'Arial, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#aab7c4'
                }
            },
            invalid: {
                color: '#fa755a',
                iconColor: '#fa755a'
            }
        };

        const card = elements.create('card', {style: style, hidePostalCode: true});
        
        card.mount('#card-element');

        // Handle real-time validation errors from the card Element
        card.addEventListener('change', ({error}) => {
            const displayError = document.getElementById('card-errors');
            if (error) {
                displayError.textContent = error.message;
            } else {
                displayError.textContent = '';
            }
        });

        // Handle form submission
        const form = document.getElementById('payment-form');
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const {paymentIntent, error} = await stripe.confirmCardPayment(
                'pi_3PLH8VBm2kKWxbxf0H37uqqO_secret_CftMF5TnAq9sT9hoHcSSE4Idp', {
                    payment_method: {
                        card: card
                    }
                }
            );

            if (error) {
                const displayError = document.getElementById('card-errors');
                displayError.textContent = error.message;
            } else {
                if (paymentIntent.status === 'succeeded') {
                    alert('Payment successful!');
                }
            }
        });
        
        <!-- 
            form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const response = await fetch('/create-payment-intent', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ amount: 1000 }) // amount in cents (e.g., $10.00)
    });

    const { clientSecret } = await response.json();

    const {paymentIntent, error} = await stripe.confirmCardPayment(
        clientSecret, {
            payment_method: {
                card: card,
                billing_details: {
                    name: 'Customer Name'
                }
            }
        }
    );

    if (error) {
        const displayError = document.getElementById('card-errors');
        displayError.textContent = error.message;
    } else {
        if (paymentIntent.status === 'succeeded') {
            alert('Payment successful!');
        }
    }
});

            -->
    </script>
</body>
</html>
