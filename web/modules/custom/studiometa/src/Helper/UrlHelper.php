<?php

namespace Drupal\studiometa\Helper;

use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class UrlHelper.
 *
 * @package Drupal\studiometa\Helper
 */
final class UrlHelper {

  /**
   * Constructs a new FilterFormatListBuilder.
   *
   * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
   *   The request stack.
   */
  public function __construct(
    protected RequestStack $requestStack
  ) {
    $this->requestStack = $requestStack;
  }

  /**
   * Check if the url provided is external.
   *
   * Check that the url do not belong to the FQDN of the site.
   *
   * @param \Drupal\Core\Url $url
   *   Url to check.
   *
   * @return bool
   *   Url is external.
   */
  public function isExternal(Url $url): bool {
    if (!$url->isExternal()) {
      return FALSE;
    }

    $request = $this->requestStack->getCurrentRequest();

    if (!$request instanceof Request) {
      throw new \RuntimeException("An exception occurred.");
    }

    $host = $this->getDomain($request->getHost());
    $url_domain = $this->getDomain($url->getUri());

    return strpos($host, $url_domain) === FALSE;
  }

  /**
   * Get the domain for an url.
   *
   * @param string $url
   *   Url to get the domain from.
   *
   * @return string
   *   Return the domain name.
   */
  private function getDomain(string $url) {
    $domain = explode('.', $url);

    /*
     * FQDN address are not taken in account.
     * The url provided must be a subdomain.
     */
    if (count($domain) < 2 || empty($domain[1])) {
      throw new \InvalidArgumentException("The url provided is not valid.");
    }

    array_shift($domain);

    return implode('.', $domain);
  }

}
