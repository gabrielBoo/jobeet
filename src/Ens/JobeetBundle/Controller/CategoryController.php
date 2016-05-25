<?php

namespace Ens\JobeetBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Ens\JobeetBundle\Entity\Category;

/**
 * Category controller.
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
   
    /**
     * @Route("/category/{slug}/{page}", name="EnsJobeetBundle_category", defaults={"_controller": "EnsJobeetBundle:Category:show", "page": 1 })
     * @Method("GET")
     */
    public function showAction($slug, $page)
    {
      $em = $this->getDoctrine()->getEntityManager();

      $category = $em->getRepository('EnsJobeetBundle:Category')->findOneBySlug($slug);

      if (!$category) {
        throw $this->createNotFoundException('Unable to find Category entity.');
      }

      $total_jobs = $em->getRepository('EnsJobeetBundle:Job')->countActiveJobs($category->getId());
      $jobs_per_page = 20;
      $last_page = ceil($total_jobs / $jobs_per_page);
      $previous_page = $page > 1 ? $page - 1 : 1;
      $next_page = $page < $last_page ? $page + 1 : $last_page;

      $category->setActiveJobs($em->getRepository('EnsJobeetBundle:Job')->getActiveJobs($category->getId(), $jobs_per_page, ($page - 1) * $jobs_per_page));

      return $this->render('EnsJobeetBundle:Category:show.html.twig', array(
        'category' => $category,
        'last_page' => $last_page,
        'previous_page' => $previous_page,
        'current_page' => $page,
        'next_page' => $next_page,
        'total_jobs' => $total_jobs
      ));
    }
    
}

