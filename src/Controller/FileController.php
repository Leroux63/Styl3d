<?php

namespace App\Controller;

use App\Entity\File;
use App\Form\FilesType;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
//use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/file')]
class FileController extends AbstractController
{
    public function __construct(
        //on se sert du parameter bag pour recuperer les infos du service yaml
        private ParameterBagInterface  $parameterBag,
        private EntityManagerInterface $entityManager,
      //  private PaginatorInterface     $paginator
    )
    {
    }

    #[Route('/', name: 'app_file_index', methods: ['GET'])]
    public function index(FileRepository $fileRepository): Response
    {
        return $this->render('file/index.html.twig', [
            'files' => $fileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, SluggerInterface $slugger): Response
    {
        $fileDirectoryPath = $this->parameterBag->get('files_directory');
        $file = new File();
        $form = $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            dump($file);
            $productFile = $form->get('fileLink')->getData();
            /**@var UploadedFile $productFile */
            if ($productFile){
                $originalFileName = pathinfo($productFile->getClientOriginalName(),PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName .'-' . uniqid() .'.'.$productFile->guessExtension();
                try {
                    $productFile->move(
                        $fileDirectoryPath,
                        $newFileName
                    );
                    $file->setName($newFileName);
                    $this->entityManager->persist($file);
                    $this->entityManager->flush();

                } catch (\Exception $e){

                }
            }
            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file/new.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_show', methods: ['GET'])]
    public function show(File $file): Response
    {
        return $this->render('file/show.html.twig', [
            'file' => $file,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_file_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, File $file, FileRepository $fileRepository): Response
    {
        $form = $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $fileRepository->save($file, true);

            return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('file/edit.html.twig', [
            'file' => $file,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_file_delete', methods: ['POST'])]
    public function delete(Request $request, File $file, FileRepository $fileRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $file->getId(), $request->request->get('_token'))) {
            $fileRepository->remove($file, true);
        }

        return $this->redirectToRoute('app_file_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/', name: 'app_file_download', methods: ['POST'])]
    public function downloadFile(string $id,FileRepository $fileRepository): BinaryFileResponse
    {
        $file = $fileRepository->find($id);

        return $this->file($_SERVER['DOCUMENT_ROOT']."/build/uploads/files/".$file->getName());
    }
}
