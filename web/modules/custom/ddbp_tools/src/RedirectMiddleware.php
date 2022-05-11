<?php

namespace Drupal\ddbp_tools;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Added this middleware for proper redirection as of core 9.2.
 * See https://www.drupal.org/i/3214949.
 */
class RedirectMiddleware implements HttpKernelInterface {

  /**
   * @var HttpKernelInterface
   */
  protected $httpKernel;
  /**
   * @var RedirectResponse
  */
  protected $redirectResponse;

  /**
   * Constructor.
   *
   * @param HttpKernelInterface $http_kernel
   */
  public function __construct(HttpKernelInterface $http_kernel) {
    $this->httpKernel = $http_kernel;
  }

  /**
   * {@inheritdoc}
   */
  public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true) {
    $response = $this->httpKernel->handle($request, $type, $catch);
    return $this->redirectResponse ?: $response;
  }

  /**
   * Stores the requested redirect response.
   *
   * @param RedirectResponse|null $redirectResponse
   */
  public function setRedirectResponse(?RedirectResponse $redirectResponse) {
    $this->redirectResponse = $redirectResponse;
  }
}