<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Google Sheet Plugin</title>

    <!-- Bootstrap core CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" crossorigin="anonymous">

  </head>

  <body>

    <!-- Begin page content -->
    <main role="main" class="container">
      <h1 class="mt-5">Google Sheet Plugin Documentation</h1>
      <p class="lead">
        This is the default setting page of the <strong>{{ $plugin->name }}</strong> plugin!
        <br>
        The page is rendered by <code>\Wikko\Googlesheet\</code>, feel free to replace it with actual content.
      </p>
      <h2>Get started</h2>
      <p class="lead">
        Follow the steps to install the plugin
      </p>
      <ul>
        <li>Install plugin when click <a href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@install') }}">here</a></li>
        <li>Activate Google Auth integration from <a href="{{ action('Admin\AuthController@index') }}">here</a></li>
        <li>Add and remove Google connections from <a href="{{ action('\Wikko\Googlesheet\Controllers\GoogleController@index') }}">here</a></li>
      </ul>
      <hr />
      <footer>
        <p class="lead">Click <a href="{{ action('\Wikko\Googlesheet\Controllers\DashboardController@index') }}">here</a> to reload the page. Or <a href="{{ action('Admin\PluginController@index') }}">click to go back to the Plugin management</a> where you can <strong>enable</strong>, <strong>disable</strong> or <strong>delete</strong> this plugin.</p>
      </footer>

    </main>



  </body>
</html>
