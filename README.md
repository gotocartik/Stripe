<hr>
<b>Stripe</b>
  
 <br>
 Stripe Payment with Curl Functions :-) 
 <br>
<img src="https://stripe.com/img/about/logos/logos/black@2x.png" width="200">
<br><br>

- <a href="https://stripe.com/" target="_blank">Sign up</a> or log into your <a href="https://manage.stripe.com" target="_blank">dashboard</a>
- Click on your profile and click on Account Settings
- Then click on **API Keys**
- Copy the **Secret Key**. and add this into `DB` or `.ENV` file

<hr> 

You can sign up for a Stripe account at https://stripe.com.

<b>Requirements</b>

PHP 5.4.0 and later.

<b>Composer</b> <br>

You can install the bindings via Composer. Run the following command:

 `composer require stripe/stripe-php`
 <hr> 
 <b>Dependencies</b> <br>
 
The bindings require the following extensions in order to work properly:

`curl`, although you can use your own non-cURL client if you prefer
`json`
`mbstring` (Multibyte String)
If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

<b>Getting Started</b>
Simple usage looks like:

`\Stripe\Stripe::setApiKey('sk_test_BQokikJOvBiI2HlWgH4olfQ2'); `<br>
`$charge = \Stripe\Charge::create(['amount' => 2000, 'currency' => 'usd', 'source' => 'tok_189fqt2eZvKYlo2CTGBeg6Uq']);` <br>
`echo $charge;`

<b>Documentation</b>
Please see <a href="https://stripe.com/docs/api">https://stripe.com/docs/api</a> for up-to-date documentation.
 
 
