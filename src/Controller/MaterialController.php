<?php

namespace App\Controller;

use App\Entity\Material;
use App\Form\MaterialType;
use App\Repository\MaterialRepository;
use App\Service\BrevoService;
use App\Service\PDFService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/material', name: 'material_')]
class MaterialController extends AbstractController
{
    public function __construct(
        private readonly TranslatorInterface    $translator,
        private readonly EntityManagerInterface $entityManager
    )
    {
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('material/index.html.twig');
    }

    #[Route('/data', name: 'data', methods: ['POST'])]
    public function getMaterialsData(Request $request, MaterialRepository $materialRepository): JsonResponse
    {
        $data = $request->request->all();
        $start = $data['start'];
        $length = $data['length'];
        $search = $data['search']['value'];

        $totalMaterials = $materialRepository->count([]);

        $showOutOfStock = $request->get('outOfStock') === 'true';

        dump('ici : ', $showOutOfStock);

        $materials = $materialRepository->findBySearch($search, $start, $length, $showOutOfStock);

        $filteredMaterialsCount = count($materials);

        $response = [
            'draw' => $request->request->get('draw'),
            'recordsTotal' => $totalMaterials,
            'recordsFiltered' => $filteredMaterialsCount,
            'data' => []
        ];

        /** @var Material $material */
        foreach ($materials as $material) {
            $response['data'][] = [
                'id' => $material->getId(),
                'name' => $material->getName(),
                'priceTaxFree' => $material->getPriceTaxFree(),
                'priceTaxIncluded' => $material->getPriceTaxIncluded(),
                'quantity' => $material->getQuantity(),
            ];
        }

        return new JsonResponse($response);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $material = new Material();
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($material);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('flash.form.edit.success', [], 'admin'));

                    return $this->redirectToRoute('material_edit', ['id' => $material->getId()]);
                } catch (\Exception $e) {
                    $this->addFlash('error', $this->translator->trans('flash.form.error', [], 'admin'));
                }
            } else {
                $this->addFlash('error', $this->translator->trans('flash.form.error', [], 'admin'));
            }
            return $this->redirectToRoute('material_index');
        }

        return $this->render('material/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Material $material): Response
    {
        $form = $this->createForm(MaterialType::class, $material);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $this->entityManager->persist($material);
                    $this->entityManager->flush();

                    $this->addFlash('success', $this->translator->trans('flash.form.edit.success', [], 'admin'));

                    return $this->redirectToRoute('material_index');
                } catch (\Exception $e) {
                    $this->addFlash('error', $e->getMessage());
                }
            } else {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('error', $error->getMessage());
                }
            }
            return $this->redirectToRoute('material_index');
        }

        return $this->render('material/edit.html.twig', [
            'form' => $form->createView(),
            'material' => $material,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Material $material): Response
    {
        try {
            $this->entityManager->remove($material);
            $this->entityManager->flush();

            $this->addFlash('success', $this->translator->trans('flash.form.delete.success', [], 'admin'));
            return $this->redirectToRoute('material_index');
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('material_edit', ['id' => $material->getId()]);
    }
    
    #[Route('/{id}/decrement', name: 'decrement_quantity')]
    public function decrementQuantity(Material $material, BrevoService $brevoService): Response
    {
        try {
            $material->decrementQuantity();
            if ($material->getQuantity() === 0) {
                $brevoService->sendOutOfStockAlertEmail($material);
                $this->addFlash('success', $this->translator->trans('flash.form.delete.success', [], 'admin'));
            } else {
                $this->addFlash('success', $this->translator->trans('flash.decrement.success', [], 'admin'));
            }
            $this->entityManager->persist($material);
            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('material_index');
    }

    #[Route('/{id}/increment', name: 'increment_quantity')]
    public function incrementQuantity(Material $material): Response
    {
        try {
            $material->incrementQuantity();
            $this->entityManager->persist($material);
            $this->entityManager->flush();
            $this->addFlash('success', $this->translator->trans('flash.increment.success', [], 'admin'));
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('material_index');
    }

    #[Route('/{id}/pdf', name: 'pdf')]
    public function generatePdf(Material $material, PDFService $pdfService): Response
    {
        $html = $this->renderView('pdf/material.html.twig', [
            'material' => $material
        ]);

        $pdfContent = $pdfService->generatePdf($html);

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="material.pdf"',
            ]
        );
    }
}
