<div class="wrap">
    <h1><?php echo $taxonomy->name ?></h1>

    <div id="poststuff">
        <form action="" method="post">
            <input name="id" type="hidden" value="<?php echo $taxonomy->id; ?>"/>
            <input name="edit-taxonomy" type="hidden" value="true" />

            <p class="label">
                <label for="content-type">Taxonomy belongs to...</label>
            </p>

            <select name="content-type[taxonomy][content-type-id]" id="content-type">
                <?php $parent_id = property_exists($taxonomy, 'parent_id') ? (int)$taxonomy->parent_id : null ?>

                <option value="">All</option>
                <?php foreach($content_types as $content_type): ?>
                    <option value="<?php echo $content_type->id ?>" <?php echo $parent_id===(int)$content_type->id ? 'selected' : '' ?>><?php echo $content_type->name;?></option>
                <?php endforeach;?>
            </select>

            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input type="submit" value="Update Content Type" class="button button-primary" />
            </p>
        </form>

        <form action="" method="post">
            <input type="hidden" name="method" value="delete" />
            <input type="hidden" name="delete-taxonomy" value="delete" />
            <input name="id" type="hidden" value="<?php echo $taxonomy->id; ?>"/>
            <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

            <p class="submit">
                <input value="Delete taxonomy" class="delete button-secondary" type="submit" onclick="return confirm('Are you sure?')"/>
            </p>
        </form>
    </div>
</div>
