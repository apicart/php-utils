<?php declare(strict_types = 1);

namespace Apicart\Utils\Tests\Http;

use Apicart\Utils\Http\Url;
use PHPUnit\Framework\TestCase;

final class UrlTest extends TestCase
{

	public const URL = 'https://user:password@www.apicart.net:1234/en/index.php?name=value#fragment';

	/**
	 * @var Url
	 */
	private $url;


	/**
	 * {@inheritdoc}
	 */
	protected function setUp(): void
	{
		$this->url = new Url(self::URL);
	}


	public function testToString(): void
	{
		self::assertSame('https://www.apicart.net:1234/en/index.php?name=value#fragment', (string) $this->url);
	}


	public function testGetScheme(): void
	{
		self::assertSame('https', $this->url->getScheme());
	}


	public function testGetUser(): void
	{
		self::assertSame('user', $this->url->getUser());
	}


	public function testGetPassword(): void
	{
		self::assertSame('password', $this->url->getPassword());
	}


	public function testGetHost(): void
	{
		self::assertSame('www.apicart.net', $this->url->getHost());
	}


	public function testGetDomain(): void
	{
		self::assertSame('apicart.net', $this->url->getDomain());
		self::assertSame('www.apicart.net', $this->url->getDomain(3));
	}


	public function testGetPort(): void
	{
		self::assertSame(1234, $this->url->getPort());
	}


	public function testGetPath(): void
	{
		self::assertSame('/en/index.php', $this->url->getPath());
	}


	public function testGetQuery(): void
	{
		self::assertSame('name=value', $this->url->getQuery());
	}


	public function testGetQueryParameter(): void
	{
		self::assertSame('value', $this->url->getQueryParameter('name'));
		self::assertSame('bar', $this->url->getQueryParameter('foo', 'bar'));
	}


	public function testGetFragment(): void
	{
		self::assertSame('fragment', $this->url->getFragment());
	}


	public function testGetAbsoluteUrl(): void
	{
		self::assertSame('https://www.apicart.net:1234/en/index.php?name=value#fragment', $this->url->getAbsoluteUrl());
	}


	public function testGetAuthority(): void
	{
		self::assertSame('www.apicart.net:1234', $this->url->getAuthority());
	}


	public function testGetHostUrl(): void
	{
		self::assertSame('https://www.apicart.net:1234', $this->url->getHostUrl());
	}


	public function testGetBasePath(): void
	{
		self::assertSame('/en/', $this->url->getBasePath());
	}


	public function testGetBaseUrl(): void
	{
		self::assertSame('https://www.apicart.net:1234/en/', $this->url->getBaseUrl());
	}


	public function testGetRelativeUrl(): void
	{
		self::assertSame('index.php?name=value#fragment', $this->url->getRelativeUrl());
	}


	public function testIsEqual(): void
	{
		self::assertTrue($this->url->isEqual(self::URL));
		self::assertFalse($this->url->isEqual('http://apicart.net'));
	}


	public function testJsonSerialize(): void
	{
		$json = json_encode($this->url);
		self::assertSame('"https:\/\/www.apicart.net:1234\/en\/index.php?name=value#fragment"', $json);
	}

}
