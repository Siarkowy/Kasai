<?php

// Usage example of Kasai to SQL PHP-driven translator <https://github.com/Siarkowy/Kasai>
// Copyright by Siarkowy, 2014. Released under the terms of BSD 2-Clause license.

require 'kasai.php';                // (1) Load parser.

if ($code = isset($_REQUEST['code']) ? htmlspecialchars($_REQUEST['code']) : null)
{
    try
    {
        $sql = Kasai::parse($code); // (2) Do the Kasai->SQL parsing.
    }
    catch (Exception $e)
    {
        $error = $e->getMessage();  // (3) Catch errors should they raise.
    }
}

?>
<!DOCTYPE html>

<html>

<head>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <title>Kasai</title>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="page-header">
            <h1><img src="kasai.png" alt="Kasai" style="vertical-align: baseline">
            Kasai <small>Cascading AI scripts</small></h1>
        </div>

<?php if (@$error): ?>
        <div class="alert alert-danger"><?php echo $error ?></div>
<?php endif ?>

<?php if (@$sql): ?>
        <pre><?php echo $sql ?></pre>
<?php endif ?>
        <form action="?" method="post" role="form">
            <textarea class="form-control" rows="15" name="code" accesskey="c">
<?php if (@$code): echo @$code; else: ?>npc 15371 Arcatraz Sentinel
{
    timerooc 0.5 {
        normal { cast 36716 self interrupt|triggered }
        heroic { cast 38828 self interrupt|triggered }
    }

    aggro {
        normal { morph 4 setphase 1 }
        heroic { morph 5 setphase 1 }
    }

    repeatable timer 5 10 10 15 { threatallpct -100 }

    # phase 1

    notphase 2 {
        health 12 { setphase 2 setflag nonattackable }
    }

    # phase 2

    notphase 1 {
        repeatable timer 0 0 8 8 {
            normal { cast 36719 self triggered }
            heroic { cast 38830 self triggered }
        }
    }

    evade { setphase 0 removeflag nonattackable }
}
<?php endif ?></textarea>
            <button type="submit" class="btn btn-primary" accesskey="t">Translate</button>
        </form>

        <div class="page-footer">
            <p class="text-center">Repository: <a href="https://github.com/Siarkowy/Kasai">https://github.com/Siarkowy/Kasai</a></p>
        </div>
    </div>
</body>

</html>
