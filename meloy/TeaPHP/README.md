#TeaPHP
 
*喝着茶，唱着歌，就能把活给干了*

*每一段代码，都能自动适应多个用途*
 
~~~
Your Code  |----- JSON Data
           |----- Remote Service
           |----- API Docs
           |----- HTML View  --|----- AngularJS
                               |----- Vue.js
                               | ......
          			
~~~


##示例
~~~php
<?php

namespace app\actions\orders;

use tea\Action;
use tea\upload\File;

class OrderAction extends Action {
	public function run(string $name, int $age, bool $drinking, File $teaFile) {
		$this->success("Thank You");
	}
}

?>
~~~

##联系我们
交流QQ群：199435611
