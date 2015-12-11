<?php

/**
 * Class Test_Adding_New_ContentType
 *
 * @group rooftop-db
 * @group rooftop-plugin
 *
 */
class Test_Adding_New_ContentType extends PHPUnit_Framework_TestCase {
    function setUp() {
        $blogs = array_values(array_reverse(wp_get_sites()));
        $blog_id = (int)$blogs[0]['blog_id'];

        switch_to_blog($blog_id);
    }

    public function testContentTypesCanBeAdded() {
        $types = Content_Type::getAll();
        $type_count = count($types);
        $new_type = new Content_Type('Test Type');
        $new_type->save();

        $this->assertEquals($type_count, $type_count+1);
    }
}
?>