<div class="container app-home-container">
    <h1><?php echo $title; ?></h1>
    <p>
        TBD - I am the application's home page when a user is logged in.
        I should not be reachable unless a user's session is stored.
    </p>
    <p>
        While I am developing this application, I temporarily have the session outputted below.
    </p>
    <pre>
        <?php var_dump($_SESSION); ?>
    </pre>
</div>
