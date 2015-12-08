<div class="wrap">
    <h1>Add new content type</h1>

    <div id="poststuff">
        <form action="" method="post">
            <div id="titlediv">
                <input type="text" name="content-type[name]" placeholder="Enter content type name here" id="title" value="" />
            </div>
            <br/>

            <div class="taxonomies-container">
                <div class="taxonomies">
                    <p>
                        <input type="text" name="content-type[taxonomies][]" placeholder="New taxonomy name" size="40" value="" />
                    </p>
                </div>

                <div class="taxonomies-container-actions">
                    <a href="#">+ Add taxonomy</a>
                </div>
            </div>

            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input type="submit" value="Create Content Type" class="button button-primary" />
            </p>
        </form>
    </div>
</div>
