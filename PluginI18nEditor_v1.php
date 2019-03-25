<?php
class PluginI18nEditor_v1{
  private $settings = null;
  function __construct($buto) {
    if($buto){
      /**¨
       * ¨Include.
       */
      wfPlugin::includeonce('wf/form_v2');
      wfPlugin::includeonce('wf/array');
      wfPlugin::includeonce('wf/yml');
      /**
       * Enable.
       */
      wfPlugin::enable('wf/bootstrap');
      wfPlugin::enable('wf/form_v2');
      wfPlugin::enable('wf/table');
      wfPlugin::enable('wf/bootstrapjs');
      wfPlugin::enable('wf/dom');
      wfPlugin::enable('datatable/datatable_1_10_16');
      wfPlugin::enable('twitter/bootstrap335v');
      wfPlugin::enable('wf/ajax');
      wfPlugin::enable('wf/callbackjson');
    /**
       * Layout path.
       */
      wfArray::set($GLOBALS, 'sys/layout_path', '/plugin/i18n/editor_v1/layout');
      /**
       * Unset i18n event for this module.
       */
      wfPlugin::event_remove('document_render_string', 'i18n/translate_v1');
      /**
       * Settings.
       */
      $this->settings = new PluginWfArray(wfArray::get($GLOBALS, 'sys/settings/plugin_modules/'.wfArray::get($GLOBALS, 'sys/class').'/settings'));
    }
  }
  /**
   * Start page.
   */
  public function page_start(){
    $page = $this->getYml('page/start');
    /**
     * Insert admin layout from theme.
     */
    $page = wfDocument::insertAdminLayout($this->settings, 1, $page);
    /**
     * 
     */
    wfDocument::mergeLayout($page->get());
  }
  /**
   * List all i18n files in a table.
   */
  public function page_list(){
    $page = $this->getYml('page/list');
    $rs = $this->get_all();
    $page->setByTag(array('data' => $rs->get()));
    wfDocument::mergeLayout($page->get());
  }
  /*
   * Edit form.
   */
  public function page_edit(){
    $page = $this->getYml('page/edit');
    wfDocument::mergeLayout($page->get());
  }
  /*
   * Capture edit form.
   */
  public function page_capture(){
    $page = $this->getYml('page/capture');
    wfDocument::mergeLayout($page->get());
  }
  /**
   * 
   */
  public function page_delete(){
    $this->delete();
    $page = $this->getYml('page/delete');
    wfDocument::mergeLayout($page->get());
  }
  /**
   * 
   */
  public function page_add(){
    $page = $this->getYml('page/add');
    wfDocument::mergeLayout($page->get());
  }
  /**
   * Render data in form.
   */
  public function edit_render($form){
    $key = urldecode(wfRequest::get('key'));
    $all = $this->get_all();
    $languages = wfConfig::getI18nLanguages();
    foreach ($languages as $k => $value) {
      $form->set("items/$value", array('type' => 'text', 'label' => $value, 'default' => $all->get("$key/$value")));
    }
    $form->setByTag(array('key' => $key));
    return $form;
  }
  /**
   * Capture data from form.
   */
  public function edit_capture($form){
    $key = (wfRequest::get('key'));
    $languages = wfConfig::getI18nLanguages();
    foreach ($languages as $k => $value) {
      $file = $this->get_file($value);
      $file->set($key, wfRequest::get($value));
      $file->save();
    }
    return array("$('.modal').modal('hide');");
  }
  private function delete(){
    $key = urldecode(wfRequest::get('key'));
    $languages = wfConfig::getI18nLanguages();
    foreach ($languages as $k => $value) {
      $file = $this->get_file($value);
      $file->setUnset($key);
      $file->save();
    }
    return null;
  }
  /**
   * Get yml file.
   * Example page/start.
   * @param string $dir
   * @return \PluginWfYml
   */
  public function getYml($dir){
    return new PluginWfYml(__DIR__.'/'.$dir.".yml");
  }
  /**
   * Get path to i18n folder.
   * @return string
   */
  private function getPathI18n(){
    /**
     * Path to translations files.
     */
    $path = '/theme/[theme]/i18n';
    /**
     * Check if path is changed via theme settings file.
     */
    $settings = wfPlugin::getPluginSettings('i18n/editor_v1', true);
    if($settings && $settings->get('settings/path')){
      $path = $settings->get('settings/path');
    }
    return $path;
  }
  /**
   * Get all translations.
   * @return \PluginWfArray
   */
  private function get_all(){
    $languages = wfConfig::getI18nLanguages();
    $class = wfGlobals::get('class');
    $rs = new PluginWfArray();
    /**
     * Get data from files.
     */
    foreach ($languages as $key => $la) {
      $file = $this->get_file($la);
      foreach ($file->get() as $key2 => $value2) {
        /**
         * Set i18n key.
         */
        $rs->set($key2.'/innerHTML', $key2);
        /**
         * Render all languages for the key to make PluginWfTable to work.
         */
        foreach ($languages as $l) {
          if(!isset($rs->array[$key2][$l])){
            $rs->set($key2.'/'.$l, null);
          }
        }
        /**
         * Set values.
         */
        $rs->set($key2.'/'.$la, $value2);
        $rs->set($key2.'/row_click', "PluginWfBootstrapjs.modal({id: 'modal_i18neditor_edit', url: '/$class/edit/key/$key2', lable: 'Edit'});");
      }
    }
    return $rs;
  }
  private function get_file($la){
    $path = wfGlobals::getAppDir(). $this->getPathI18n();
    $file = new PluginWfYml($path.'/'.$la.'.yml');
    return $file;
  }
}
