<?php declare(strict_types = 1);

namespace Apicart\Utils\Http;

use InvalidArgumentException;
use JsonSerializable;

/**
 * @author David Grudl (https://nette.org/)
 *
 * URI Syntax (RFC 3986)
 *
 * scheme  user  password   host   port basePath   relativeUrl
 *   |      |      |         |       |    |             |
 * /--\   /--\ /------\ /---------\ /--\/--\/----------------------------\
 * http://user:password@apicart.net:1234/en/index.php?name=value#fragment  <-- absoluteUrl
 *        \____________________________/\____________/^\________/^\______/
 *                     |                       |           |         |
 *                 authority                  path       query    fragment
 *
 * - authority:   [user[:password]@]host[:port]
 * - hostUrl:     http://user:password@apicart.net:8042
 * - basePath:    /en/ (everything before relative URI not including the script name)
 * - baseUrl:     http://user:password@apicart.net:8042/en/
 */
final class Url implements JsonSerializable
{

	/**
	 * @var array
	 */
	public static $defaultPorts = [
		'http' => 80,
		'https' => 443,
		'ftp' => 21,
		'news' => 119,
		'nntp' => 119,
	];

	/**
	 * @var string
	 */
	private $scheme = '';

	/**
	 * @var string
	 */
	private $user = '';

	/**
	 * @var string
	 */
	private $password = '';

	/**
	 * @var string
	 */
	private $host = '';

	/**
	 * @var int|null
	 */
	private $port;

	/**
	 * @var string
	 */
	private $path = '';

	/**
	 * @var array
	 */
	private $query = [];

	/**
	 * @var string
	 */
	private $fragment = '';


	/**
	 * @param  string $url
	 * @throws InvalidArgumentException if URL is malformed
	 */
	public function __construct($url = null)
	{
		if (is_string($url)) {
			$p = @parse_url($url); // @ - is escalated to exception
			if ($p === false) {
				throw new InvalidArgumentException("Malformed or unsupported URI '${url}'.");
			}

			$this->scheme = $p['scheme'] ?? '';
			$this->port = $p['port'] ?? null;
			$this->host = isset($p['host']) ? rawurldecode($p['host']) : '';
			$this->user = isset($p['user']) ? rawurldecode($p['user']) : '';
			$this->password = isset($p['pass']) ? rawurldecode($p['pass']) : '';
			$this->setPath($p['path'] ?? '');
			$this->setQuery($p['query'] ?? []);
			$this->fragment = isset($p['fragment']) ? rawurldecode($p['fragment']) : '';
		}
	}


	/**
	 * @return string
	 */
	public function __toString()
	{
		return $this->getAbsoluteUrl();
	}


	/**
	 * Sets the scheme part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setScheme($value)
	{
		$this->scheme = (string) $value;
		return $this;
	}


	/**
	 * Returns the scheme part of URI.
	 * @return string
	 */
	public function getScheme()
	{
		return $this->scheme;
	}


	/**
	 * Sets the user name part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setUser($value)
	{
		$this->user = (string) $value;
		return $this;
	}


	/**
	 * Returns the user name part of URI.
	 * @return string
	 */
	public function getUser()
	{
		return $this->user;
	}


	/**
	 * Sets the password part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setPassword($value)
	{
		$this->password = (string) $value;
		return $this;
	}


	/**
	 * Returns the password part of URI.
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}


	/**
	 * Sets the host part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setHost($value)
	{
		$this->host = (string) $value;
		$this->setPath($this->path);
		return $this;
	}


	/**
	 * Returns the host part of URI.
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}


	/**
	 * Returns the part of domain.
	 * @return string
	 */
	public function getDomain(int $level = 2)
	{
		$parts = ip2long($this->host) !== false ? [$this->host] : explode('.', $this->host);
		$parts = $level >= 0 ? array_slice($parts, -$level) : array_slice($parts, 0, $level);
		return implode('.', $parts);
	}


	/**
	 * Sets the port part of URI.
	 * @param  int $value
	 * @return static
	 */
	public function setPort(int $value)
	{
		$this->port = $value;
		return $this;
	}


	/**
	 * Returns the port part of URI.
	 * @return int|null
	 */
	public function getPort()
	{
		return $this->port ?: (isset(self::$defaultPorts[$this->scheme]) ? self::$defaultPorts[$this->scheme] : null);
	}


	/**
	 * Sets the path part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setPath($value)
	{
		$this->path = (string) $value;
		if ($this->host && substr($this->path, 0, 1) !== '/') {
			$this->path = '/' . $this->path;
		}
		return $this;
	}


	/**
	 * Returns the path part of URI.
	 * @return string
	 */
	public function getPath()
	{
		return $this->path;
	}


	/**
	 * Sets the query part of URI.
	 * @param  string $value|array
	 * @return static
	 */
	public function setQuery($value)
	{
		$this->query = is_array($value) ? $value : self::parseQuery($value);
		return $this;
	}


	/**
	 * Appends the query part of URI.
	 * @param  string $value|array
	 * @return static
	 */
	public function appendQuery($value)
	{
		$this->query = is_array($value)
			? $value + $this->query
			: self::parseQuery($this->getQuery() . '&' . $value);
		return $this;
	}


	/**
	 * Returns the query part of URI.
	 * @return string
	 */
	public function getQuery()
	{
		return http_build_query($this->query, '', '&', PHP_QUERY_RFC3986);
	}


	/**
	 * @return array
	 */
	public function getQueryParameters()
	{
		return $this->query;
	}


	/**
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function getQueryParameter($name, $default = null)
	{
		return isset($this->query[$name]) ? $this->query[$name] : $default;
	}


	/**
	 * @param string $name
	 * @param mixed $value null unsets the parameter
	 * @return static
	 */
	public function setQueryParameter($name, $value)
	{
		$this->query[$name] = $value;
		return $this;
	}


	/**
	 * Sets the fragment part of URI.
	 * @param  string $value
	 * @return static
	 */
	public function setFragment($value)
	{
		$this->fragment = (string) $value;
		return $this;
	}


	/**
	 * Returns the fragment part of URI.
	 * @return string
	 */
	public function getFragment()
	{
		return $this->fragment;
	}


	/**
	 * Returns the entire URI including query string and fragment.
	 * @return string
	 */
	public function getAbsoluteUrl()
	{
		$query = $this->getQuery();

		return $this->getHostUrl() . $this->path
			. ($query !== '' ? '?' . $query : '')
			. ($this->fragment === '' ? '' : '#' . $this->fragment);
	}


	/**
	 * Returns the [user[:pass]@]host[:port] part of URI.
	 * @return string
	 */
	public function getAuthority()
	{
		return $this->host === ''
			? ''
			: ($this->user !== '' && $this->scheme !== 'http' && $this->scheme !== 'https'
				? rawurlencode($this->user) . ($this->password === '' ? '' : ':' . rawurlencode($this->password)) . '@'
				: '')
			. $this->host
			. ($this->port !== null
				&& (! isset(self::$defaultPorts[$this->scheme]) || $this->port !== self::$defaultPorts[$this->scheme])
				? ':' . $this->port
				: '');
	}


	/**
	 * Returns the scheme and authority part of URI.
	 * @return string
	 */
	public function getHostUrl()
	{
		return ($this->scheme ? $this->scheme . ':' : '')
			. (($authority = $this->getAuthority()) || $this->scheme ? '//' . $authority : '');
	}


	/**
	 * Returns the base-path.
	 * @return string
	 */
	public function getBasePath()
	{
		$pos = strrpos($this->path, '/');
		return $pos === false ? '' : substr($this->path, 0, $pos + 1);
	}


	/**
	 * Returns the base-URI.
	 * @return string
	 */
	public function getBaseUrl()
	{
		return $this->getHostUrl() . $this->getBasePath();
	}


	/**
	 * Returns the relative-URI.
	 * @return string
	 */
	public function getRelativeUrl()
	{
		return (string) substr($this->getAbsoluteUrl(), strlen($this->getBaseUrl()));
	}


	/**
	 * URL comparison.
	 * @param  string $url|self
	 * @return bool
	 */
	public function isEqual($url)
	{
		$url = new self($url);
		$query = $url->query;
		ksort($query);
		$query2 = $this->query;
		ksort($query2);
		$http = in_array($this->scheme, ['http', 'https'], true);
		return $url->scheme === $this->scheme
			&& strcasecmp($url->host, $this->host) === 0
			&& $url->getPort() === $this->getPort()
			&& ($http || $url->user === $this->user)
			&& ($http || $url->password === $this->password)
			&& self::unescape($url->path, '%/') === self::unescape($this->path, '%/')
			&& $query === $query2
			&& $url->fragment === $this->fragment;
	}


	/**
	 * Transforms URL to canonical form.
	 * @return static
	 */
	public function canonicalize()
	{
		$this->path = preg_replace_callback(
			'#[^!$&\'()*+,/:;=@%]+#',
			function ($m) { return rawurlencode($m[0]);
			},
			self::unescape($this->path, '%/')
		);
		$this->host = strtolower($this->host);
		return $this;
	}


	/**
	 * @return string
	 */
	public function jsonSerialize()
	{
		return $this->getAbsoluteUrl();
	}


	/**
	 * Similar to rawurldecode, but preserves reserved chars encoded.
	 * @param  string $s decode
	 * @param  string $reserved characters
	 * @return string
	 */
	public static function unescape($s, $reserved = '%;/?:@&=+$,')
	{
		// reserved (@see RFC 2396) = ";" | "/" | "?" | ":" | "@" | "&" | "=" | "+" | "$" | ","
		// within a path segment, the characters "/", ";", "=", "?" are reserved
		// within a query component, the characters ";", "/", "?", ":", "@", "&", "=", "+", ",", "$" are reserved.
		if ($reserved !== '') {
			$s = preg_replace_callback(
				'#%(' . substr(chunk_split(bin2hex($reserved), 2, '|'), 0, -1) . ')#i',
				function ($m) { return '%25' . strtoupper($m[1]);
				},
				$s
			);
		}
		return rawurldecode($s);
	}


	/**
	 * Parses query string.
	 * @return array
	 */
	public static function parseQuery(string $s)
	{
		parse_str($s, $res);
		return $res;
	}

}
