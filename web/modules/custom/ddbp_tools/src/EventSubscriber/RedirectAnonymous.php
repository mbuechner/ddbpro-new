<?php

namespace Drupal\ddbp_tools\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Drupal\Core\Routing\CurrentRouteMatch;
use Drupal\Core\Session\AccountProxy;

/**
 * Redirect anonymous users away from some CTs & TXs.
 */
class RedirectAnonymous implements EventSubscriberInterface {

  protected $routeMatch;
  protected $currentUser;

  /**
   * RedirectAnonymous constructor.
   *
   * @param CurrentRouteMatch $current_route_match
   * @param AccountProxy $account_proxy
   */
  public function __construct(CurrentRouteMatch $current_route_match, AccountProxy $account_proxy) {
    $this->routeMatch = $current_route_match;
    $this->currentUser = $account_proxy->getAccount();
  }

  /**
   * Returns an array of event names we want to listen to.
   *
   * @return array
   */
  static function getSubscribedEvents(): array {
    $events[KernelEvents::RESPONSE][] = ['doRedirect'];
    return $events;
  }

  /**
   * Deny access for anonymous users.
   *
   * @param ResponseEvent $event
   */
  public function doRedirect(ResponseEvent $event) {
    // Exit if there is an exception.
    if ($event->getRequest()->get('exception') != NULL) {
      return;
    }

    // CTs that shouldn't be accessible as stand-alone pages
    $target_CTs = [
      'overview_teaser'
    ];

    // TX terms (views) that shouldn't be accessible as stand-alone pages
    $target_TXs = [];

    $is_anonymous = $this->currentUser->isAnonymous();
    $node = $this->routeMatch->getParameter('node');
    $term = $this->routeMatch->getParameter('taxonomy_term');

    if ($is_anonymous && $event->getRequestType() === HttpKernelInterface::MASTER_REQUEST) {
      if ($node && in_array($node->getType(), $target_CTs)) {
        throw new AccessDeniedHttpException();
      }

      if ($term && in_array($term->bundle(), $target_TXs)) {
        throw new AccessDeniedHttpException();
      }
    }
  }
}
