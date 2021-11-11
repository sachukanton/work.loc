<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type"
              content="text/html; charset=utf-8" />
        @isset($_subject)
            <title>{{ $_subject }}</title>
        @endisset
    </head>
    <body style="margin:0;padding:0;color:#3a3c4c;font-family:Roboto,sans-serif;">
        @yield('body')
    </body>
</html>
