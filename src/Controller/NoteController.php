<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\NoteFormatter;

class NoteController extends AbstractController
{
    #[Route('/note', name: 'note_index')]
    public function index(NoteRepository $repository): Response
    {
        $notes = $repository->findAll();

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
        ]);
    }

    #[Route('/note/{id}', name: 'note_show')]
    public function show(Note $note, NoteFormatter $formatter): Response
    {
        $formattedNote = $formatter->format($note);
        return $this->render('note/show.html.twig', [
            'note' => $formattedNote
        ]);
    }

    #[Route('/note/new', name: 'note_new')]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($note);
            $manager->flush();
            $this->addFlash(
                'notice',
                'Note was created successfully'
            );

            return $this->redirectToRoute('product_show', [
                'id' => $note->getId()
            ]);
        }

        return $this->render('note/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/note/{id}/edit', name: 'note_edit')]
    public function edit(Note $note, Request $request, EntityManagerInterface $manager): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->flush();
            $this->addFlash(
                'notice',
                'Note was updated successfully'
            );

            return $this->redirectToRoute('product_show', [
                'id' => $note->getId()
            ]);
        }

        return $this->render('note/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/note/{id}/delete', name: 'note_edit')]
    public function delete(Request $request, Note $note, EntityManagerInterface $manager): Response
    {
        if($request->isMethod('POST')) {
            $manager->remove($note);
            $manager->flush();
            $this->addFlash(
                'notice',
                'Note was deleted successfully!'
            );
            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/delete.html.twig', [
            'id' => $note->getId()
        ]);
    }


}
