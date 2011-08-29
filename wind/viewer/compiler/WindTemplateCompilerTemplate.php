<?php
Wind::import('COM:viewer.AbstractWindTemplateCompiler');
/**
 * <template source='' suffix='' load='false' />
 * source: 模板文件源地址
 * suffix: 模板文件后缀
 * load: 是否将编译内容加载到本模板中
 * 
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qiong Wu <papa0924@gmail.com>
 * @version $Id$
 * @package 
 */
class WindTemplateCompilerTemplate extends AbstractWindTemplateCompiler {
	
	protected $source = '';
	
	protected $suffix = '';
	
	protected $load = 'true';

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::compile()
	 */
	public function compile($key, $content) {
		if (!$this->source)
			return $content;
		
		preg_match('/^{?\$(\w+)}?$/Ui', $this->source, $_tmp);
		if (!empty($_tmp)) {
			$_tpl = $this->windViewerResolver->getWindView()->templateName;
			$this->source = Wind::getApp()->getResponse()->getData($_tpl, $_tmp[1]);
		}
		if ($this->load === 'false') {
			list($compileFile) = $this->windViewerResolver->compile($this->source, $this->suffix);
			if (!empty($_tmp))
				$compileFile = str_replace($this->source, '{$' . $_tmp[1] . '}', $compileFile);
			$content = '<?php include("' . addslashes($compileFile) . '"); ?>';
		} else {
			list(, $content) = $this->windViewerResolver->compile($this->source, $this->suffix, 
				true);
		}
		return $content;
	}

	/* (non-PHPdoc)
	 * @see AbstractWindTemplateCompiler::getProperties()
	 */
	public function getProperties() {
		return array('source', 'suffix', 'load');
	}

}

?>