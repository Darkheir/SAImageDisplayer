<?php

class SAImageDisplayer extends CWidget 
{

    /**
     * The type of width and height we would like
     * to display. The name and the sizes have to be
     * defined in the config.
     */
    public $size = "original";

    /**
     * Class of the href link
     */
    public $class = "";

    /**
     * Id of the href link
     */
	public $id = "";

    /**
     * Title of the href link
     */
	public $title = null;

    /**
     * Alternative text of the href link
     */
    public $alt = null;

    /**
     * Name of the image to display
     */
    public $image = null;

    /**
     * Path to the dir where all the plugin will store
     * and get the images.
     * the root is "webroot"
     */
    public $baseDir = "images";

    /**
     * Name of the folder keeping the originals images
     */
    public $originalFolderName = "originals";

    /**
     * Name of the default image to display
     * if the given image doesn't exist
     */
    public $defaultImage = null;

    /**
     * All the possible sizes that the plugin can generate
     * when not using groups
     * Example:
     * array(
     *  'tiny' => array('width'=>40, 'height'=>30),
     *   'big' => array('width'=>640, 'height'=>480),
     *   'thumb' => array('width'=>400, 'height'=>300),
     *  ),
     *  will give the following structure:
     *  images/
     *      big/
     *      originals/
     *      thumb/
     *      tiny/
     *      
     */
    public $sizes = array();


    /**
     * List of the groups of images. Each group will have its own sizes
     * A folder will be created for each group containing all the different sizes
     * Example:
     * array(
     *     'news' => array(
     *         'tiny' => array('width'=>40, 'height'=>30),
     *         'big' => array('width'=>640, 'height'=>480),
     *       ),
     *     'reviews' => array(
     *         'thumb' => array('width'=>400, 'height'=>300),
     *      ), 
     *  ),
     *  will give the following structure:
     *  images/
     *      news/
     *          big/
     *          originals/
     *          tiny/
     *      reviews/
     *          originals/
     *          tiny/     
     */
    public $groups = array();

    /**
     * Name of the group.
     * A group let the user separate images not belonging 
     * to the same type.
     * When group is defined original images are stored in 
     * "baseDir/group/originalFolderName"
     * For example if the group "news" is defined,
     * The images will be stored in images/news/originals
     */
    public $group = null;

    /**
     * Weither or not we need to display the image tag
     * If false is selected then the image will be resized and
     * all the other actions will be performed except displaying
     * the image tag.
     * @var boolean
     */
    public $displayImage = true;

    
    private $_originalFile;
    private $_src;
    private $_width;
    private $_height;
    private $_basePath;
    private $_imageFile;

    public function init() 
    {
        $this->setBasePath();
		if( $this->size !== 'original') {
			$this->setWidthAndHeight();
			$this->checkIfFolderExists();
			$this->defineImageFile($this->size);
			$this->createImagesIfNotExists();
			$this->defineSrc($this->size);
		} else {
			$this->defineImageFile($this->originalFolderName);
			$this->defineSrc($this->originalFolderName);
		}
    }

    public function run() 
    {
        if($this->displayImage) {
            echo '<img src="' . Yii::app()->baseUrl . '/' . $this->_src .
                     '" title="' . $this->getTitle() . 
                     '" alt="' . $this->getAlt() . 
                     '" id="' . $this->id .
                     '" width="' . $this->_width .
                     '" height="' . $this->_height . 
                     '" class="' . $this->class . '" />';
        }
    }

    /**
     * Set the src to the file
     */
    private function defineSrc($imageFolder) 
    {
        $this->_src = $this->baseDir . '/'. $imageFolder . '/' . $this->image;
    }

    /**
     * Define the image file that can be the one given to the widgets
     * or the default one if the previous one doesn't exist.
     * Throw an error if the image given doesn't exist and no default image is defined
     */
    private function defineImageFile($imageFolder) 
    {
        if (!$this->image) {
            throw new Exception('Image cannot be null!');
        }
        $this->_originalFile = $this->_basePath . '/' . $this->originalFolderName . '/' . $this->image;
        if(!file_exists($this->_originalFile) && $this->defaultImage !== null){
            $this->image = $this->defaultImage;
            $this->_originalFile = $this->_basePath . '/' . $this->originalFolderName . '/' . $this->image;
        } 
        if (!file_exists($this->_originalFile)) {
            throw new Exception($this->image . ' do not exists!');
        }
        $this->_imageFile = $this->_basePath . '/' . $imageFolder . '/' . $this->image;
    }

    /**
     * Create the resized image if it doesn't exist
     */
    private function createImagesIfNotExists() 
    {
        if (!file_exists($this->_imageFile)) {
            $image = Yii::app()->image->load($this->_originalFile);
            $image->resize($this->_width, $this->_height);
            $image->save($this->_imageFile);
        }
    }

    /**
     * Check if the image is set and the folder exists or can be created
     */
    private function checkIfFolderExists() 
    {
        $path = $this->_basePath . '/' . $this->size;
        if (!file_exists($path)) {
            if(!mkdir($path, 0777, true)) {
                throw new Exception($path . ' do not exists and can\'t be created!');
            }
        }
    }

    /**
     * Check if the size setted exists and is valid
     * Load the params from the conf or set the default values
     */
    private function setBasePath() 
    {
        if($this->group !== null) {
            $this->baseDir = $this->baseDir . '/' . $this->group; 
        }
        $this->_basePath = YiiBase::getPathOfAlias('webroot') . '/' . $this->baseDir;
    }

    private function setWidthAndHeight() 
    {
        if($this->group !== null){
            $size =$this->groups[$this->group][$this->size];
        } else {
            $size = $this->sizes[$this->size];
        }
        if($size == null) {
            throw new Exception($this->size . ' is not a valid size type!');
        } elseif ($size['width'] == null) {
            throw new Exception('The width is not defined for this size type!');
        } elseif ($size['height'] == null) {
            throw new Exception('The height is not defined for this size type!');
        }
        $this->_width = $size['width'];
        $this->_height = $size['height'];
    }

    private function getTitle() {
        return $this->title ? $this->title : $this->image;
    }

    private function getAlt() {
        return $this->alt ? $this->alt : $this->image;
    }

}