<div class="wrap">
    <h1>Add new taxonomy</h1>

    <div id="poststuff">
        <form action="" method="post">
            <input name="new-taxonomy" type="hidden" value="true" />

            <div id="titlediv">
                <input type="text" name="content-type[taxonomy][name]" placeholder="Enter taxonomy name here" id="title" value="" />
            </div>
            <br/>

            <p class="label">
                <label for="content-type">Taxonomy belongs to...</label>
            </p>
            <select name="content-type[taxonomy][content-type-id]" id="content-type">
                <option value="">All</option>
                <?php foreach($content_types as $content_type): ?>
                    <option value="<?php echo $content_type->id ?>"><?php echo $content_type->name;?></option>
                <?php endforeach;?>
            </select>

            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input type="submit" value="Create Content Type" class="button button-primary" />
            </p>
        </form>
    </div>
</div>
