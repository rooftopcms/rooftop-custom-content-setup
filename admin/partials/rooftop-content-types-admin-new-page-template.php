<div class="wrap">
    <h1>Add new page template</h1>

    <div id="poststuff">
        <form action="" method="post">
            <input name="new-page-template" type="hidden" value="true" />

            <div id="titlediv">
                <input type="text" name="content-type[page-template][name]" placeholder="Enter template name here" id="title" value="" />
            </div>

            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input type="submit" value="Create Page Template" class="button button-primary" />
            </p>
        </form>
    </div>
</div>
