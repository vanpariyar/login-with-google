# WP Google Login

Minimal plugin which allows WP user to login with google.

#### How to use it?

1. Create project from [Google console](https://console.developers.google.com/apis/dashboard) if not exists.
2. Go to **Credentials** tab, And create credential for OAuth client.
    * Application type will be **Web application**
    * Add `YOUR_DOMAIN/wp-login.php` in **Authorized redirect URIs**
3. This will give you Client ID and Secret key.
4. Use below snippet, replace value of const with client id and secret. And add that in to `wp-config.php` 

```php
define( 'WP_GOOGLE_LOGIN_CLIENT_ID', 'YOUR_GOOGLE_CLIENT_ID' );
define( 'WP_GOOGLE_LOGIN_SECRET', 'YOUR_SECRET_KEY' );
```

#### How to enable user registration?
By default, user registration defers to `Settings > General Settings > Membership` if constant is not set. To enable that you can simply define const `WP_GOOGLE_LOGIN_USER_REGISTRATION` and set value `true`. If constant is set then it won't take Membership setting into account. See below example:

**Note:** If this const is set to `true` then, it will register user even when WordPress default setting, under `Settings > General Settings > Membership > Anyone can register` checkbox is OFF.

```php
define( 'WP_GOOGLE_LOGIN_USER_REGISTRATION', true );
```

#### How to restrict user registration to one or more domain(s)?

By default, when you enable user registration via constant `WP_GOOGLE_LOGIN_USER_REGISTRATION`, it will create a user for any Google login (including gmail.com users). If you are planning to use this plugin on a private, internal site, then you may like to restrict user registration to users under Google Suite organization. This configuration variable does that.

Add your domain name, without any schema prefix and `www,` as the value of `WP_GOOGLE_LOGIN_WHITELIST_DOMAINS` const. You can whitelist multiple domains. Please separate domains with commas. See below example:

**Note:** If a user already exists then it will allow a user to login with Google regardless of its domain is whitelisted or not. It will only prevent the user from registering with an email address with a domain that not whitelisted.
 
```php
define( 'WP_GOOGLE_LOGIN_WHITELIST_DOMAINS', 'example.com, sample.com' );
```

## wp-config.php parameters list

|                                   | Type    | Description                                                                                                                                                                                                                       |
|-----------------------------------|---------|-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| WP_GOOGLE_LOGIN_CLIENT_ID         | String  | Google client ID of your application.                                                                                                                                                                                             |
| WP_GOOGLE_LOGIN_SECRET            | String  | Secret key of your application                                                                                                                                                                                                    |
| WP_GOOGLE_LOGIN_USER_REGISTRATION | Boolean | (Optional) Set True If you want to enable new user registration. By default, user registration defers to `Settings > General Settings > Membership` if constant is not set.                                                       |
| WP_GOOGLE_LOGIN_WHITELIST_DOMAINS | String  | (Optional) Domain name, if you want to restrict login with your custom domain. By default, It will allow all domains. You can whitelist multiple domains.                                                                         |
| WP_GOOGLE_LOGIN_USE_MAIN_SITE_URL | Boolean | (Optional) Used for MU sites, It will use top level domain for authentication. So, you don't have to add every site's URL into Google console as authorized URL.,Note : Do not use, if your main site use SSL and sub site isn't. |
