<div class="wrap">
    <h2>
        Content types & Taxonomies
        <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new" class="page-title-action">Add New Content Type</a>
    </h2>

    <?php if(count($content_types)):?>
        <table class="wp-list-table widefat fixed striped content-types">
            <thead>
            <tr>
                <th>Content Type</th>
                <th>Taxonomies</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach($content_types as $content_type): ?>
                <tr>
                    <td class="column-title column-primary">
                        <a href="?page=<?php echo $this->plugin_name ?>-overview&action=edit&id=<?php echo $content_type->id; ?>"><?php echo $content_type->name;?></a>
                    </td>

                    <td>
                        <?php
                        if( property_exists($content_type, 'taxonomies') ) {
                            $taxonomy_names = array_map(function($tax) {
                                return $tax->name;
                            }, $content_type->taxonomies);

                            echo implode(', ', $taxonomy_names);
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php else:?>
        <p>
            You haven't added any content types endpoints yet. <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new">Add a new content type</a>.
        </p>
    <?php endif; ?>

    <br/><br/>
    <h2>Global taxonomies <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new-taxonomy" class="page-title-action">Add New Taxonomy</a></h2>
    <?php if( count($taxonomies) ):?>
        <table class="wp-list-table widefat fixed striped taxonomies">
            <thead>
                <th>Name</th>
            </thead>

            <tbody>
            <?php foreach( $taxonomies as $id => $taxonomy): ?>
                <tr>
                    <td>
                        <a href="?page=<?php echo $this->plugin_name ?>-overview&action=edit-taxonomy&id=<?php echo $taxonomy->id; ?>"><?php echo $taxonomy->name;?></a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php else:?>
        <p>
            You haven't added any global taxonomies yet. <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new-taxonomy">Add a new taxonomy</a>.
        </p>
    <?php endif;?>

    <br/><br/>
    <h2>Page Templates <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new-page-template" class="page-title-action">Add New Page Template</a></h2>
    <?php if( count($page_templates) ): ?>
        <table class="wp-list-table widefat fixed striped page_templates">
            <thead>
            <th>Name</th>
            </thead>

            <tbody>
            <?php foreach( $page_templates as $id => $page_template): ?>
                <tr>
                    <td>
                        <a href="?page=<?php echo $this->plugin_name ?>-overview&action=edit-page-template&id=<?php echo $page_template->id; ?>"><?php echo $page_template->name;?></a>
                    </td>
                </tr>
            <?php endforeach;?>
            </tbody>
        </table>
    <?php else:?>
        <p>
            You haven't added any page templates yet. <a href="?page=<?php echo $this->plugin_name ?>-overview&action=new-page-template">Add New Page Template</a> .
        </p>
    <?php endif; ?>
</div>
