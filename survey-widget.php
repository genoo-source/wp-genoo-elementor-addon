<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}
use Genoo\Utils\Strings;
use WPME\ApiExtension\Surveys;
use WPME\ApiFactory;
use WPME\CacheFactory;
use WPME\RepositoryFactory;
use WPME\Extensions\RepositorySurveys;

class Survey_Widget extends \Elementor\Widget_Base
{
    /**
     * Get widget name.
     *
     * Retrieve oEmbed widget name.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget name.
     */
    public function get_name()
    {
        return 'Survey';
    }
    /**
     * Get widget title.
     *
     * Retrieve oEmbed widget title.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget title.
     */
    public function get_title()
    {
        return __('Survey', 'Genoo Elementor Extension');
    }
    /**
     * Get widget icon.
     *
     * Retrieve oEmbed widget icon.
     *
     * @since 1.0.0
     * @access public
     *
     * @return string Widget icon.
     */
    public function get_icon()
    {
        return 'fa fa-poll';
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

    //get survey list 

    public static function getsurvey()
    {
        global $WPME_CACHE;
        $repositorySettings = new WPME\RepositorySettingsFactory();
        $repositorySurveys = new RepositorySurveys(
            $WPME_CACHE,
            new Surveys($repositorySettings)
            );
        $val = $repositorySurveys->getSurveysArrayTinyMCE();
        $survey = array();
        foreach ($val as $value) {
            if ($value['value'] == '') {
                $survey[0] = 'Select a Survey';
            }
            else {
                $survey[$value['value']] = $value['text'];
            }

        if($value['value']=='')
        {
            $survey[0] = 'Select a Survey';  
        }
        else
        {
            $survey[$value['value']]=$value['text']; 
        }
    } 
      
    return $survey;
        
}

    /**
     * Register survey widget controls.
     *
     * Adds different input fields to allow the user to change and customize the widget settings.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function _register_controls()
    {
        $getsurveyoption = array_keys($this->getsurvey());
        $this->start_controls_section(
            'content_section',
        [
            'label' => __('Survey', 'Genoo Elementor Extension'),
            'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
        ]);
        $this->add_control(
            'survey_list',
        [
            'label' => esc_html__('Survey', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::SELECT,
            'options' => $this->getsurvey(),
            'default' => $getsurveyoption[0],
        ]
        );

        $this->add_control(
            'shortcode',
        [
            'label' => esc_html__('Insert your shortcode here', 'Genoo Elementor Extension'),
            'type' => \Elementor\Controls_Manager::HIDDEN,
            'placeholder' => '',
            'default' => '',
        ]
        );
        $this->end_controls_section();
    }
    /**
     * Render survey widget output on the frontend.
     *
     * Written in PHP and used to generate the final HTML.
     *
     * @since 1.0.0
     * @access protected
     */
    protected function render()
    {
        $survey_list = $this->get_settings('survey_list');
        if ($this->get_settings('survey_list') != '') {
            if (GENOO_SETUP) {
	    $shortcodes = '[genooSurvey id="' . $survey_list . '"]';
	     }
            else {
                $shortcodes = '[WPMKTENGINESurvey id="' . $survey_list . '"]';
            }
            $shortcode = do_shortcode(shortcode_unautop($shortcodes));
?>
<div class="elementor-shortcode">
    <?php echo $shortcode; ?>
</div>
<?php
        }
    }

}
