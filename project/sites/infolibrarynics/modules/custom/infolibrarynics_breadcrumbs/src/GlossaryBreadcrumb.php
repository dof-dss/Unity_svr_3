<?php

namespace Drupal\infolibrarynics_breadcrumbs;

/**
 * @file
 * Generates the breadcrumb trail for search page(s)
 *
 * In the format:
 * > Home
 * > current-page-title
 *
 * > <front>
 * > /current-page-title
 */
use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Controller\TitleResolverInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * {@inheritdoc}
 */
class GlossaryBreadcrumb implements BreadcrumbBuilderInterface {

  /**
   * RequestStack service object.
   *
   * @var \Symfony\Component\HttpFoundation\RequestStack
   */
  protected $request;

  /**
   * The title resolver.
   *
   * @var \Drupal\Core\Controller\TitleResolverInterface
   */
  protected $titleResolver;

  /**
   * Class constructor.
   */
  public function __construct(RequestStack $request, TitleResolverInterface $title_resolver) {
    $this->request = $request;
    $this->titleResolver = $title_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('request_stack'),
      $container->get('title_resolver')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {
    $match = FALSE;
    $route_name = $route_match->getRouteName();
    $view_names = [
      'view.glossary_of_terms.glossary_page',
    ];

    foreach ($view_names as $view_name) {
      if ($route_name == $view_name) {
        $match = TRUE;
      }
    }
    return $match;

  }

  /**
   * {@inheritdoc}
   */
  public function build(RouteMatchInterface $route_match) {
    $breadcrumb = new Breadcrumb();
    $title_resolver = $this->titleResolver->getTitle($this->request->getCurrentRequest(), $route_match->getRouteObject());
    $links[] = Link::createFromRoute(t('Home'), '<front>');
    $links[] = Link::fromTextAndUrl('Resources', Url::fromUri('internal:/resources'));
    $links[] = Link::createFromRoute($title_resolver, '<none>');
    $breadcrumb->setLinks($links);
    $breadcrumb->addCacheContexts(['url.path']);

    return $breadcrumb;
  }

}
