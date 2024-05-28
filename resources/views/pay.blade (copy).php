<template>
  <div class="container">
    <h2>Make a Payment</h2>
    <form @submit.prevent="handleSubmit">
      <div class="form-group">
        <label for="card-element">Credit or debit card</label>
        <div id="card-element"></div>
        <div id="card-errors" role="alert">{{ cardErrors }}</div>
      </div>
      <button type="submit">Submit Payment</button>
    </form>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import { loadStripe } from '@stripe/stripe-js';

export default {
  name: 'PaymentForm',
  setup() {
    const stripePromise = loadStripe('your-publishable-key-here');
    const cardElement = ref(null);
    const cardErrors = ref('');

    onMounted(async () => {
      const stripe = await stripePromise;
      const elements = stripe.elements();
      const card = elements.create('card', {
        style: {
          base: {
            color: '#32325d',
            fontFamily: 'Arial, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4',
            },
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a',
          },
        },
      });
      card.mount('#card-element');

      card.on('change', (event) => {
        if (event.error) {
          cardErrors.value = event.error.message;
        } else {
          cardErrors.value = '';
        }
      });

      cardElement.value = card;
    });

    const handleSubmit = async () => {
      const stripe = await stripePromise;

      const response = await fetch('/create-payment-intent', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ amount: 1000 }), // amount in cents (e.g., $10.00)
      });

      const { clientSecret } = await response.json();

      const { error, paymentIntent } = await stripe.confirmCardPayment(clientSecret, {
        payment_method: {
          card: cardElement.value,
          billing_details: {
            name: 'Customer Name',
          },
        },
      });

      if (error) {
        cardErrors.value = error.message;
      } else {
        if (paymentIntent.status === 'succeeded') {
          alert('Payment successful!');
        }
      }
    };

    return {
      handleSubmit,
      cardErrors,
    };
  },
};
</script>

<style>
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








import { createApp } from 'vue';
import App from './App.vue';
import PaymentForm from './components/PaymentForm.vue';

createApp(App).component('PaymentForm', PaymentForm).mount('#app');










<template>
  <div id="app">
    <PaymentForm />
  </div>
</template>

<script>
export default {
  name: 'App',
};
</script>

<style>
/* Add any global styles here */
</style>








