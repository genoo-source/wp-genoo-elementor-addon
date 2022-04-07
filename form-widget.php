<?php
use WPMKTENGINE\Wordpress\Utils;
use Genoo\Utils\Strings;
use WPMKTENGINE\RepositoryForms;
use WPME\RepositoryFactory;
use WPME\RepositorySettingsFactory;
use WPMKTENGINE\Cache;
use WPME\ApiFactory;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Form_Widget extends \Elementor\Widget_Base {

    /**
     * Get widget name.
     *
     * Retrieve Form_Widget widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
public function get_name() 
{
    return 'Form';
}

    /**
     * Get widget title.
     *
     * Retrieve Form_Widget widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
public function get_title() 
{
return __('Form', 'Genoo Elementor Extension' );
}

    /**
     * Get widget icon.
     *
     * Retrieve Form_Widget widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
public function get_icon() 
{
    return 'fa fa-poll-h';
}

    /**
     * Get widget categories.
     *
     * Retrieve the list of categories the Form_Widget widget belongs to.
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

 
     //get form options
public function getformoption()
{
    $cache = isset($cache) ? $cache : new \WPME\CacheFactory(WPMKTENGINE_CACHE);
    $repositarySettings = new RepositorySettingsFactory();
    $api = isset($api) ? $api : new \WPME\ApiFactory($repositarySettings);
    $repositaryForms = new RepositoryForms($cache, $api);
    $formvalue = $repositaryForms->getFormsArrayTinyMCE();
    $form_option=array();
    foreach($formvalue as $values)
    {
        if($values['value']=='')
        {
            $form_option[0] = 'Select a Form';  
        }
        else
        {
            $form_option[$values['value']] = $values['text'];    
        }
    }
    return $form_option;
}
  /**
     * Register Form widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
protected function _register_controls() 
{
    $getformoption = array_keys($this->getformoption());
    $getstyleoption = array_keys($this->styleoption());
  
    $this->start_controls_section(
       'content_section',
        [
            'label' => __('Form', 'Genoo Elementor Extension' ),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
    $this->add_control(
       'form_list',
       [
            'label' => esc_html__('Form', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' =>  $this->getformoption(),
            'default' => $getformoption[0],
       ]);
    $this->add_control(
        'Style',
        [
            'label' => esc_html__('Style', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' =>  $this->styleoption(),
            'default' => $getstyleoption[0],
        
        ]);
    $this->add_control(
		'show_title',
		[
			'label' => __('Display confirmation message inline?', 'Genoo Elementor Extension' ),
			'type' => \Elementor\Controls_Manager::SWITCHER,
			'label_on' => __('Show', 'Genoo Elementor Extension' ),
			'label_off' => __('Hide', 'Genoo Elementor Extension' ),
			'return_value' => 'yes',
			'default' => 'No',
		]);
    $this->add_control(
		'success_message',
		[
			'label' => __('Form success message', 'Genoo Elementor Extension' ),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'rows' => 6,
			'default' => __('', 'Genoo Elementor Extension' ),
			'placeholder' => __('Type your description here', 'Genoo Elementor Extension' ),
		]);
        
    $this->add_control(
		'error_message',
		[
			'label' => __('Form error message', 'Genoo Elementor Extension'),
			'type' => \Elementor\Controls_Manager::TEXTAREA,
			'rows' => 6,
			'default' => __('', 'Genoo Elementor Extension' ),
			'placeholder' => __('Type your description here', 'Genoo Elementor Extension'),
		]);
	$this->add_control(
        'shortcode',
			[
				'label' => esc_html__('Insert your shortcode here', 'Genoo Elementor Extension'),
				'type' =>  \Elementor\Controls_Manager::HIDDEN,
				'placeholder' => '',
				'default' => '',
			]
		);
        
       $this->end_controls_section();
      

}
    
//get style options
public static function styleoption()
{
    $repo = new \WPMKTENGINE\RepositorySettings();
    $repositoryForms = $repo->getSettingsThemes();
    return $repositoryForms;
       
}

    /**
     * Render Form_Widget widget output on the frontend.
      *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
protected function render() 
{
    $settings = $this->get_settings_for_display();

    $form_list = $this->get_settings('form_list');
        
    $theme = $this->get_settings('Style');
        
    $html_checkbox =   $this->get_settings('Checkbox');
        
    if ( 'yes' === $settings['show_title'] ) {
		$confirmation = "true";
		$success_message =   $this->get_settings('success_message');
        $error_message =  $this->get_settings('error_message');
		}
	else {
		$confirmation = "false";   
	}
       
   if($this->get_settings('form_list') != '')
    {    
        if(GENOO_SETUP)
        {
            $shortcodes = '[genooForm id="'.$form_list.'" theme="'.$theme.'" confirmation="'.$confirmation.'" msgSuccess="'.$success_message.'" msgFail="'.$error_message.'"]';
        }
        else{
         
    $shortcodes = '[WPMKTENGINEForm id="'.$form_list.'" theme="'.$theme.'" confirmation="'.$confirmation.'" msgSuccess="'.$success_message.'" msgFail="'.$error_message.'"]';
    }
}
	$shortcode = do_shortcode( shortcode_unautop( $shortcodes ) ); ?>
    	<div class="elementor-shortcode"><?php echo $shortcode; ?></div>    
    <?php
}

}
 