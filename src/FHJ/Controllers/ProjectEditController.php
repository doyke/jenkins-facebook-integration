<?php

namespace FHJ\Controllers;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Form;
use Symfony\Component\Validator\Constraints as Assert;
use FHJ\Entities\Project;
use FHJ\Facebook\FacebookDataRetriever;

/**
 * ProjectEditController
 * @package FHJ\Controllers
 */
class ProjectEditController extends BaseController {

    const ROUTE_PROJECT_EDIT = 'projectEdit';
    
    const ROUTE_PROJECT_NEW = 'projectNew';

    public function editAction(Request $request, Project $project) {
        $form = $this->defineEditForm($project);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            return $this->processEdit($form, $project);
        }
        
        return $this->getTemplateEngine()->render('projectEdit.html.twig', array(
            'form' => $form->createView(),
            'project' => $project,
            'cancelPath' => $this->generateRoute(ProjectListController::ROUTE_PROJECT_LIST_OWN)
        )); 
    }
    
    public function newAction(Request $request) {
        $form = $this->defineCreateForm();
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            return $this->processEdit($form, null);
        }
        
        return $this->getTemplateEngine()->render('projectNew.html.twig', array(
            'form' => $form->createView(),
            'cancelPath' => $this->generateRoute(ProjectListController::ROUTE_PROJECT_LIST_OWN)
        )); 
    }
    
    private function processEdit(Form $form, Project $originalProject = null) {
        // fetches the supplied Project object which already has been modified
        $editedProject  = $form->getData();
            
        try {
            if ($originalProject === null) {
                $user = $this->getSecurity()->getToken()->getUser();

                $this->getProjectRepository()->createProject($user, $editedProject->getTitle(),
                    $editedProject->getDescription(), $editedProject->getFacebookGroupId());
            } else {
                if ($originalProject->getId() !== $editedProject->getId()) {
                    throw new \RuntimeException(sprintf(
                        'project objects do not match: edited "%d" and original "%d"',
                        $editedProject->getId(), $originalProject->getId()));
                }
                
                $this->getProjectRepository()->updateProject($editedProject);
            }
        } catch (\Exception $e) {
            $this->getLogger()->addError(sprintf('error when saving project with id "%d"',
                $originalProject->getId()), array('exception' => $e));
            
            $this->getSession()->getFlashBag()->add('error', sprintf(
                'The project "%s" could not be saved to the database!', $editedProject->getTitle()));
            return $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
        }
        
        $this->getSession()->getFlashBag()->add('success', sprintf('The project "%s" has been successfully saved!',
                $editedProject->getTitle()));
        return $this->doRedirect(ProjectListController::ROUTE_PROJECT_LIST_OWN);
    }
    
    private function defineCreateForm() {
        $form = $this->getFormFactory()->createBuilder('form', new Project(0, 0, '-'));
        $facebookData = new FacebookDataRetriever($this->getFacebookObject(), $this->getLogger());

	    $validGroups = $facebookData->getGroups();
	    $groups = array('-' => 'No group selected') + $validGroups;

        $form->add('title', 'text', array(
                'label' => 'Project title',
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5, 'max' => 100))),
            ))->add('description', 'textarea', array(
                'label' => 'Description',
                'constraints' => new Assert\Length(array('max' => 600)),
            ))->add('facebookGroupId', 'choice', array(
                'label' => 'Post messages to following facebook group',
                'choices' => $groups,
		        'constraints' => new Assert\Choice(array_keys($validGroups)),
            ))->add('save', 'submit', array(
                'label' => 'Create project'    
            ))->add('cancel', 'button', array(
		        'label' => 'Cancel'
	        ));
            
        return $form->getForm();
    }
    
    private function defineEditForm(Project $project) {
        $form = $this->getFormFactory()->createBuilder('form', $project);
        $facebookData = new FacebookDataRetriever($this->getFacebookObject(), $this->getLogger());

	    $validGroups = $facebookData->getGroups();
	    $groups = array('-' => 'No group selected') + $validGroups;

	    $form->add('title', 'text', array(
                'label' => 'Project title',
                'constraints' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 5, 'max' => 100))),
            ))->add('description', 'textarea', array(
                'label' => 'Description',
                'constraints' => new Assert\Length(array('max' => 600)),
            ))->add('facebookGroupId', 'choice', array(
                'label' => 'Post messages to following facebook group',
                'choices' => $groups,
			    'constraints' => new Assert\Choice(array_keys($validGroups)),
            ))->add('enabled', 'checkbox', array(
                'label' => 'Posting of messages enabled?'
            ))->add('secretKey', 'text', array(
                'label' => 'Secret key',
                'disabled' => true,
            ))->add('svnplotDbPath', 'text', array(
                'label' => 'Absolute path to SVNPlot database file'
            ))->add('lastBuildState', 'text', array(
                'label' => 'Last known Jenkins build state',
                'disabled' => true
            ))->add('save', 'submit', array(
                'label' => 'Save changes'    
            ));
            
        return $form->getForm();
    }
    
}