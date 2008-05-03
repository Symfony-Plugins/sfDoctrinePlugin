<?php
/*
 * This file is part of the sfDoctrinePlugin package.
 * (c) 2006-2007 Jonathan H. Wage <jwage@mac.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineConfigHandler
 *
 * Parses the doctrine.yml and produces a config php file
 *
 * @package    sfDoctrinePlugin
 * @author     Jonathan H. Wage <jwage@mac.com>
 * @version    SVN: $Id$
 */
class sfDoctrineConfigHandler extends sfYamlConfigHandler
{
  /**
   * execute
   *
   * @param string $configFiles
   * @return void
   */
  public function execute($configFiles)
  {
    $configs = $this->parseYamls($configFiles);

    $env = sfConfig::get('sf_environment');
    $defaultConfig = isset($configs['all']) ? $configs['all']:array();
    $envConfig = isset($configs[$env]) ? $configs[$env]:array();

    $config = sfToolKit::arrayDeepMerge($defaultConfig, $envConfig);

    $globalCode = $this->buildPhpCodeArray('global', $config);

    $connectionCode = array();
    if (isset($config['connections']))
    {
      foreach ($config['connections'] as $name => $connectionConfig)
      {
        $connectionCode = array_merge($connectionCode, $this->buildPhpCodeArray($name, $connectionConfig));
      }
    }

    $code = sprintf("<?php\n" .
                    "// auto-generated by sfDoctrineConfigHandler\n" .
                    "// date: %s\n%s\n", date('Y-m-d H:i:s'), implode("\n", $globalCode)."\n".implode("\n", $connectionCode));

    return $code;
  }

  /**
   * Build the php code array for the doctrine php config file
   *
   * @param string $name 
   * @param array $config 
   * @return array $data
   */
  public function buildPhpCodeArray($name, $config)
  {
    $data = array();

    $data[] = "\$config['".$name."_attributes'] = array();";
    $data[] = "\$config['".$name."_listeners'] = array();";
    $data[] = "\$config['".$name."_record_listeners'] = array();";
    $data[] = null;

    if (isset($config['attributes']) && !empty($config['attributes']))
    {
      foreach ($config['attributes'] as $key => $value)
      {
        if (is_bool($value))
        {
          $val = $value ? 'true':'false';
        } else {
          $val = "Doctrine::" . strtoupper($key) . '_' . strtoupper($value);
        }

        $data[] = "\$config['".$name."_attributes']['$key'] = $val;";
      }
    }

    if (isset($config['listeners']) && !empty($config['listeners']))
    {
      foreach ($config['listeners'] as $listener)
      {
        $data[] = "\$config['".$name."_listeners'][] = '$listener';";
      }
    }

    if (isset($config['record_listeners']) AND !empty($config['record_listeners']))
    {
      foreach ($config['record_listeners'] as $modelName => $listener)
      {
        $data[] = "\$config['".$name."_record_listeners']['$modelName'] = '$listener';";
      }
    }

    return $data;
  }
}