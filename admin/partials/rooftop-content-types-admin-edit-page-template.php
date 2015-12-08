<div class="wrap">
    <h1><?php echo $page_template->name ?></h1>

    <div id="poststuff">
        <form action="" method="post">
            <input name="id" type="hidden" value="<?php echo $page_template->id; ?>"/>
            <input name="edit-page-template" type="hidden" value="true" />

            <input type="text" name="content-type[page-template][name]" value="<?php echo $page_template->name; ?>"/>

            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input type="submit" value="Update Page Template" class="button button-primary" />
            </p>
        </form>

        <form action="" method="post">
            <input type="hidden" name="method" value="delete" />
            <input type="hidden" name="delete-page-template" value="delete" />
            <input name="id" type="hidden" value="<?php echo $page_template->id; ?>"/>
            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input value="Delete page template" class="delete button-secondary" type="submit" onclick="return confirm('Are you sure?')"/>
            </p>
        </form>
    </div>
</div>
