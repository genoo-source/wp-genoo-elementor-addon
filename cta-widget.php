<?php
use WPMKTENGINE\CTA,
    WPMKTENGINE\Utils\Strings,
    WPMKTENGINE\WidgetForm,
    WPMKTENGINE\WidgetCTA,
    WPMKTENGINE\ModalWindow,
    WPMKTENGINE\RepositoryCTA,
    WPMKTENGINE\Wordpress\Attachment,
    WPMKTENGINE\Wordpress\Post;
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Cta_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve cta widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
public function get_name() 
{
    return 'CTA';
}

    /**
     * Get widget title.
     *
     * Retrieve cta widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
public function get_title() 
{
 return __( 'CTA', 'Genoo Elementor Extension' );
}

    /**
     * Get widget icon.
     *
     * Retrieve cta widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
public function get_icon() 
{
    return 'fa fa-share-square';
}

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the oEmbed widget belongs to.
     *
     * @since 1.0.0
     * @access public
     *
     * @return array Widget categories.
     */
public function get_categories() 
{
    return ['Genoo-elementor'];
}


     //get all available cta options 
public function getctaform()
{
   
    $repo = new RepositoryCTA(); 
    $hascta = $repo->getArrayTinyMCE();
    $options = array();
    foreach($hascta as $cta)
    {
    $options[$cta['value']] = $cta['text']; 
    }
    return $options;

}
 /**
     * Register cta widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */ 
protected function _register_controls()
{
   $this->start_controls_section(
   'content_section',
    [
        'label' => __( 'CTA', 'Genoo Elementor Extension' ),
        'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
    ]);
    $this->add_control(
    'CTA',
    [
        'label' => esc_html__('CTA', 'Genoo Elementor Extension'),
        'type' => \Elementor\Controls_Manager::SELECT,
        'options' =>  $this->getctaform(),
        'default' => '',
    ]);
            
    $this->add_control(
       'Align',
         [
                'label' => esc_html__( 'Align', 'Genoo Elementor Extension' ),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                'none'  => __('None', 'Genoo Elementor Extension' ),
                'left' => __('Left', 'Genoo Elementor Extension' ),
                'right' => __('Right', 'Genoo Elementor Extension' ),
                'center' => __('Center', 'Genoo Elementor Extension' ),
                           
                             ],
          ]);
        
    $this->add_control(
	    'show_title',
		    [
                'label' => __( 'Display confirmation message inline?', 'Genoo Elementor Extension' ),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __( 'Show', 'Genoo Elementor Extension' ),
                'label_off' => __( 'Hide', 'Genoo Elementor Extension' ),
                'return_value' => 'yes',
                'default' => 'No',
		    ]);
    
    $this->add_control(
        
        'CTAappearanceinterval',
			[
				'label' => __( 'CTA appearance interval', 'Genoo Elementor Extension' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'default' => __( '', 'Genoo Elementor Extension' ),
			]
		);

    $this->add_control(
              
			'shortcode',
			[
				'label' => esc_html__( 'Insert your shortcode here', 'Genoo Elementor Extension' ),
				'type' =>  \Elementor\Controls_Manager::HIDDEN,
				'placeholder' => '',
				'default' => '',
			]
		);


	$this->end_controls_section();
  
} 
 /**
     * Render oEmbed widget output on the frontend.
      *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */

protected function render() 
{
    $settings = $this->get_settings_for_display();
    $cta_list = $this->get_settings('CTA');
    $align = $this->get_settings('Align');
    $cta_checkbox = $this->get_settings( 'Cta_checkbox' );
        
    if ( 'yes' === $settings['show_title']) 
    {
        $hastime="true";
        $ctaappearanceinterval = $this->get_settings('CTAappearanceinterval');
    } 
    else
    {
        $hastime="false";  
    }
        
    if($this->get_settings('CTA') != '')
    {    
        if(GENOO_SETUP)
        {
            $shortcodes = '[genooCTA id="'.$cta_list.'" align="'.$align.'" hastime="'.$hastime.'" time="'.$ctaappearanceinterval.'"]';
        }
        else{
           $shortcodes = '[WPMKTENGINECTA id="'.$cta_list.'" align="'.$align.'" hastime="'.$hastime.'" time="'.$ctaappearanceinterval.'"]';
        }
       $shortcode = do_shortcode( shortcode_unautop( $shortcodes ) );
	?>
	<div class="elementor-shortcode"><?php echo $shortcode; ?></div>
<?php
}
}
}
 