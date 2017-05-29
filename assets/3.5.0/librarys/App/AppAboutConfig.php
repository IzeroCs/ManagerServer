<?php

    namespace Librarys\App;

    use Librarys\Boot;
    use Librarys\File\FileInfo;
    use Librarys\App\Base\BaseConfigRead;

    final class AppAboutConfig extends BaseConfigRead
    {

        public function __construct(Boot $boot, $pathConfigSystem = null)
        {
            parent::__construct($boot, $pathConfigSystem, env('config_file_name.about'));
            parent::parse(true);
        }

    }

?>