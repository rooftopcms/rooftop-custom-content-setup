<div class="wrap">
    <h1>Edit <?php echo $content_type->name; ?></h1>
    <?php
        $inflector = ICanBoogie\Inflector::get('en');
        $sanitised = str_replace(" ","_",strtolower($content_type->name));
        $route = "/wp-json/wp/v2/{$inflector->pluralize($sanitised)}";
    ?>

    <p>Available at <a href="<?php echo $route ?>"><?php echo $route ?></a></p>

    <form action="?page=<?php echo $this->plugin_name;?>-overview" method="POST" id="content-type">
        <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>
        <input name="id" type="hidden" value="<?php echo $content_type->id; ?>"/>

        <table class="wp-list-table widefat fixed striped pages">
            <thead>
                <tr>
                    <td style="width: 50px">
                        Delete
                    </td>

                    <td scope="col" class="column-primary">
                        Taxonomy
                    </td>
                </tr>
            </thead>

            <tbody>
            <?php foreach($content_type->taxonomies as $taxonomy): ?>
                <tr>
                    <th scope="row" class="check-column">
                        <input name="content-type[taxonomies][<?php echo $taxonomy->id;?>][_destroy]" type="checkbox"/>
                    </th>

                    <td>
                        <?php echo $taxonomy->name; ?>
                    </td>
                </tr>
            <?php endforeach;?>
                <tr>
                    <td></td>
                    <td>
                        <input type="text" name="content-type[taxonomies][][name]" placeholder="Taxonomy name" value=""/>
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <input type="submit" value="Update Content Type" class="button button-primary" />
        </p>
    </form>

    <form action="?page=<?php echo $this->plugin_name; ?>-overview" method="POST" id="content-type">
        <?php wp_nonce_field( $this->plugin_name, $this->plugin_name.'-token' ); ?>

        <input name="method" value="delete" type="hidden"/>
        <input name="id" type="hidden" value="<?php echo $content_type->id; ?>"/>

        <p class="submit">
            <input value="Delete content type & taxonomies" class="delete button-secondary" type="submit" onclick="return confirm('Are you sure?')"/>
        </p>
    </form>

</div>