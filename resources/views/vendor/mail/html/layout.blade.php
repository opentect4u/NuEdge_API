<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
    
@media only screen and (max-width: 600px) {
.inner-body {
width: 100% !important;
}

.footer {
width: 100% !important;
}
}

@media only screen and (max-width: 500px) {
.button {
width: 100% !important;
}
}

@keyframes blinker {
    50% {
        opacity: 0;
    }
}

@keyframes clippath {

0%,
100% {
    clip-path: inset(0 0 98% 0);
}

25% {
    clip-path: inset(0 98% 0 0);
}

50% {
    clip-path: inset(98% 0 0 0);
}

75% {
    clip-path: inset(0 0 0 98%);
}
}
</style>
</head>
<body>

    @yield('content')

</body>
</html>
