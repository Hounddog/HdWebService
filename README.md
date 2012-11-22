this module is intended to be a base WebService module to be consumed by 3rd Party modules intending to create webservice connection wrappers

## Usage

Setup your Webservice Client via config in Module.php

This is an example for EdpGithub.

```
public function getServiceConfig() {
    return array(
        'factories' => array(
            'EdpGithub\Client' => function($sm) {
                $client = $sm->get('HdWebService\Client');
                $client->setNamespace('EdpGithub\Api');
            },
            'EdpGithub\HttpClient' => function($sm) {
                $client = $sm->get('HdWebService\HttpClient');
                $client->setOptions($sm->get('edpgithub_options'));
            }
            'edpgithub_options' => function($sm) {
                $config = $sm->get('Config');
                return new HdWebService\Options\ModuleOptions(isset($config['edpgithub']) ? $config['edpgithub'] : array())
            },
        );
    );
}
```

`$client->setNamespace('EdpGithub\Api');` This set's the namespace where to find your Api Classes.

Example Api Setup in module.config.php

```
return array(
    'edpgithub' => array(
        'base_url' => 'https://api.github.com/',
        'timeout' => '10',
        'api_version' => 'beta',
    ),
    'service_manager' => array(
        'invokables' => array(
            'EdpGithub\Api\User'        => 'EdpGithub\Api\User',
        ),
    ),
);
```

Example Api Class

```
namespace EdpGithub\Api;

use HdWebService\Api\AbstractApi;

class User extends AbstractApi
{
    /**
     * Get A single user
     *
     * @link http://developer.github.com/v3/users/
     *
     * @param  string $username
     * @return array
     */
    public function show($username)
    {
        return $this->get('users/'.urlencode($username));
    }
}
```

In your application you can now use it as follows:
```
$client = $sm->get('EdpGithub\Client');
$result = $client->api('user')->show('username');