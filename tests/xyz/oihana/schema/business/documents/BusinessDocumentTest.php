<?php

namespace tests\xyz\oihana\schema\business\documents ;

use PHPUnit\Framework\TestCase;
use ReflectionException;

use oihana\reflect\Reflection;

use org\schema\Intangible;
use org\schema\Organization;
use org\schema\ParcelDelivery;
use org\schema\Person;
use org\schema\Place;
use org\schema\PostalAddress;
use org\schema\QuantitativeValue;

use xyz\oihana\schema\business\documents\Adjustment;
use xyz\oihana\schema\business\documents\BusinessDocument;
use xyz\oihana\schema\business\documents\BusinessDocumentLine;
use xyz\oihana\schema\business\documents\DocumentTotals;
use xyz\oihana\schema\business\documents\PaymentSchedule;
use xyz\oihana\schema\business\documents\TaxDetail;
use xyz\oihana\schema\constants\Oihana;
use xyz\oihana\schema\enumerations\BusinessDocumentAuthority;
use xyz\oihana\schema\enumerations\BusinessDocumentDirection;
use xyz\oihana\schema\enumerations\BusinessDocumentStatus;
use xyz\oihana\schema\enumerations\DocumentTotalsAccuracy;

class BusinessDocumentTest extends TestCase
{
    public function testIsIntangible(): void
    {
        $this->assertInstanceOf( Intangible::class , new BusinessDocument() );
    }

    public function testContextConstant(): void
    {
        $this->assertSame( Oihana::SCHEMA , BusinessDocument::CONTEXT );
    }

    public function testTraitConstants(): void
    {
        $this->assertSame( 'adjustments'    , BusinessDocument::ADJUSTMENTS     );
        $this->assertSame( 'assignedSeller' , BusinessDocument::ASSIGNED_SELLER );
        $this->assertSame( 'attachments'    , BusinessDocument::ATTACHMENTS     );
        $this->assertSame( 'author'         , BusinessDocument::AUTHOR          );
        $this->assertSame( 'authority'      , BusinessDocument::AUTHORITY       );
        $this->assertSame( 'billingAddress' , BusinessDocument::BILLING_ADDRESS );
        $this->assertSame( 'contact'        , BusinessDocument::CONTACT         );
        $this->assertSame( 'currency'       , BusinessDocument::CURRENCY        );
        $this->assertSame( 'customer'       , BusinessDocument::CUSTOMER        );
        $this->assertSame( 'direction'      , BusinessDocument::DIRECTION       );
        $this->assertSame( 'documentLines'  , BusinessDocument::DOCUMENT_LINES  );
        $this->assertSame( 'issueDate'      , BusinessDocument::ISSUE_DATE      );
        $this->assertSame( 'orderDelivery'  , BusinessDocument::ORDER_DELIVERY  );
        $this->assertSame( 'paymentTerms'   , BusinessDocument::PAYMENT_TERMS   );
        $this->assertSame( 'pointOfSale'    , BusinessDocument::POINT_OF_SALE   );
        $this->assertSame( 'references'     , BusinessDocument::REFERENCES      );
        $this->assertSame( 'seller'         , BusinessDocument::SELLER          );
        $this->assertSame( 'status'         , BusinessDocument::STATUS          );
        $this->assertSame( 'taxes'          , BusinessDocument::TAXES           );
        $this->assertSame( 'totals'         , BusinessDocument::TOTALS          );
        $this->assertSame( 'totalsAccuracy' , BusinessDocument::TOTALS_ACCURACY );
        $this->assertSame( 'volume'         , BusinessDocument::VOLUME          );
        $this->assertSame( 'weight'         , BusinessDocument::WEIGHT          );

        $this->assertSame( Oihana::ADJUSTMENTS     , BusinessDocument::ADJUSTMENTS     );
        $this->assertSame( Oihana::BILLING_ADDRESS , BusinessDocument::BILLING_ADDRESS );
        $this->assertSame( Oihana::CONTACT         , BusinessDocument::CONTACT         );
        $this->assertSame( Oihana::CUSTOMER        , BusinessDocument::CUSTOMER        );
        $this->assertSame( Oihana::ORDER_DELIVERY  , BusinessDocument::ORDER_DELIVERY  );
        $this->assertSame( Oihana::POINT_OF_SALE   , BusinessDocument::POINT_OF_SALE   );
        $this->assertSame( Oihana::TOTALS          , BusinessDocument::TOTALS          );
        $this->assertSame( Oihana::VOLUME          , BusinessDocument::VOLUME          );
        $this->assertSame( Oihana::WEIGHT          , BusinessDocument::WEIGHT          );

        // the document names the salesperson the same way an organization does
        $this->assertSame( Oihana::ASSIGNED_SELLER , BusinessDocument::ASSIGNED_SELLER );
    }

    public function testDefaults(): void
    {
        $document = new BusinessDocument() ;

        $this->assertNull( $document->adjustments    ?? null );
        $this->assertNull( $document->assignedSeller ?? null );
        $this->assertNull( $document->attachments    ?? null );
        $this->assertNull( $document->author         ?? null );
        $this->assertNull( $document->authority      ?? null );
        $this->assertNull( $document->billingAddress ?? null );
        $this->assertNull( $document->contact        ?? null );
        $this->assertNull( $document->currency       ?? null );
        $this->assertNull( $document->customer       ?? null );
        $this->assertNull( $document->direction      ?? null );
        $this->assertNull( $document->documentLines  ?? null );
        $this->assertNull( $document->issueDate      ?? null );
        $this->assertNull( $document->orderDelivery  ?? null );
        $this->assertNull( $document->paymentTerms   ?? null );
        $this->assertNull( $document->pointOfSale    ?? null );
        $this->assertNull( $document->references     ?? null );
        $this->assertNull( $document->seller         ?? null );
        $this->assertNull( $document->status         ?? null );
        $this->assertNull( $document->taxes          ?? null );
        $this->assertNull( $document->totals         ?? null );
        $this->assertNull( $document->totalsAccuracy ?? null );
        $this->assertNull( $document->volume         ?? null );
        $this->assertNull( $document->weight         ?? null );
    }

    public function testConstructorHydratesScalarProperties(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::CURRENCY   => 'EUR' ,
            BusinessDocument::ISSUE_DATE => '2026-01-15' ,
            BusinessDocument::STATUS     => BusinessDocumentStatus::DRAFT ,
            BusinessDocument::DIRECTION  => BusinessDocumentDirection::SALE ,
            BusinessDocument::CUSTOMER   => new Person([ 'name' => 'Jane Doe' ]) ,
            BusinessDocument::SELLER     => new Organization([ 'name' => 'ACME Supplies' ]) ,
            BusinessDocument::AUTHOR     => new Organization([ 'name' => 'ACME Supplies' ]) ,
        ]);

        $this->assertSame( 'EUR' , $document->currency ) ;
        $this->assertSame( '2026-01-15' , $document->issueDate ) ;
        $this->assertSame( BusinessDocumentStatus::DRAFT , $document->status ) ;
        $this->assertSame( BusinessDocumentDirection::SALE , $document->direction ) ;
        $this->assertInstanceOf( Person::class , $document->customer ) ;
        $this->assertInstanceOf( Organization::class , $document->seller ) ;
        $this->assertInstanceOf( Organization::class , $document->author ) ;
    }

    /**
     * A document that says nothing about its authority is ours : the property
     * only ever states the exception.
     * @throws ReflectionException
     */
    public function testAuthorityIsAbsentOnAnOwnDocument(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::STATUS => BusinessDocumentStatus::DRAFT ,
        ]);

        $this->assertNull( $document->authority ) ;
    }

    /**
     * A harvested document says it explicitly, and the value survives beside
     * the direction and the status it is orthogonal to.
     * @throws ReflectionException
     */
    public function testAuthorityHydratesMirrored(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::AUTHORITY => BusinessDocumentAuthority::MIRRORED ,
            BusinessDocument::DIRECTION => BusinessDocumentDirection::SALE     ,
            BusinessDocument::STATUS    => BusinessDocumentStatus::ACCEPTED    ,
        ]);

        $this->assertSame( BusinessDocumentAuthority::MIRRORED , $document->authority ) ;
        $this->assertSame( BusinessDocumentDirection::SALE     , $document->direction ) ;
        $this->assertSame( BusinessDocumentStatus::ACCEPTED    , $document->status    ) ;
    }

    /**
     * A document says nothing about the accuracy of its totals until it is told
     * to — and that silence is only silence : it does not read as `EXACT`.
     * @throws ReflectionException
     */
    public function testTotalsAccuracyIsAbsentUntilStated(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::TOTALS => [ 'total' => [ 'value' => 1240 , 'currency' => 'EUR' ] ] ,
        ]);

        $this->assertNull( $document->totalsAccuracy ) ;
    }

    /**
     * A document carrying something nobody could price says its amounts are a
     * floor — while the amounts themselves stay whole, which is the whole point
     * of stating it beside them rather than inside them.
     * @throws ReflectionException
     */
    public function testTotalsAccuracyHydratesMinimum(): void
    {
        $document = new Reflection()->hydrate
        (
            [
                BusinessDocument::TOTALS          => [ 'total' => [ 'value' => 1240 , 'currency' => 'EUR' ] ] ,
                BusinessDocument::TOTALS_ACCURACY => DocumentTotalsAccuracy::MINIMUM ,
            ],
            BusinessDocument::class
        );

        $this->assertSame( DocumentTotalsAccuracy::MINIMUM , $document->totalsAccuracy ) ;
        $this->assertInstanceOf( DocumentTotals::class , $document->totals ) ;
        $this->assertSame( 1240 , $document->totals->total->value ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionHydratesNestedValueObjects(): void
    {
        $document = new Reflection()->hydrate
        (
            [
                BusinessDocument::DOCUMENT_LINES  => [ [ 'position' => 1 , 'quantity' => 2 ] ] ,
                BusinessDocument::ADJUSTMENTS     => [ [ 'type' => 'discount' , 'percentage' => 5 , 'reason' => 'Order-level discount' ] ] ,
                BusinessDocument::TAXES           => [ [ 'category' => 'VAT' , 'rate' => 20.0 ] ] ,
                BusinessDocument::TOTALS          => [ 'subtotal' => [ 'value' => 100 , 'currency' => 'EUR' ] ] ,
                BusinessDocument::PAYMENT_TERMS   => [ 'installments' => [ [ 'dueDate' => '2026-02-01' , 'percentage' => 100.0 ] ] ] ,
                BusinessDocument::BILLING_ADDRESS => [ 'streetAddress' => '1 rue du Test' , 'postalCode' => '12000' , 'addressLocality' => 'Rodez' ] ,
                BusinessDocument::CONTACT         => [ 'name' => 'Jane Doe' ] ,
                BusinessDocument::ORDER_DELIVERY  => [ 'trackingNumber' => 'PKG-1' , 'expectedArrivalFrom' => '2026-07-21' ] ,
                BusinessDocument::POINT_OF_SALE   => [ 'name' => 'Rodez' ] ,
            ],
            BusinessDocument::class
        );

        $this->assertInstanceOf( BusinessDocumentLine::class , $document->documentLines[ 0 ] ) ;
        $this->assertSame( 1 , $document->documentLines[ 0 ]->position ) ;

        $this->assertInstanceOf( PostalAddress::class , $document->billingAddress ) ;
        $this->assertSame( '1 rue du Test' , $document->billingAddress->streetAddress ) ;

        $this->assertInstanceOf( Person::class , $document->contact ) ;
        $this->assertSame( 'Jane Doe' , $document->contact->name ) ;

        $this->assertInstanceOf( ParcelDelivery::class , $document->orderDelivery ) ;
        $this->assertSame( 'PKG-1' , $document->orderDelivery->trackingNumber ) ;

        $this->assertInstanceOf( Place::class , $document->pointOfSale ) ;
        $this->assertSame( 'Rodez' , $document->pointOfSale->name ) ;

        $this->assertInstanceOf( Adjustment::class , $document->adjustments[ 0 ] ) ;
        $this->assertSame( 5 , $document->adjustments[ 0 ]->percentage ) ;
        $this->assertSame( 'Order-level discount' , $document->adjustments[ 0 ]->reason ) ;

        $this->assertInstanceOf( TaxDetail::class , $document->taxes[ 0 ] ) ;

        $this->assertInstanceOf( DocumentTotals::class , $document->totals ) ;
        $this->assertInstanceOf( \org\schema\MonetaryAmount::class , $document->totals->subtotal ) ;

        $this->assertInstanceOf( PaymentSchedule::class , $document->paymentTerms ) ;
        $this->assertCount( 1 , $document->paymentTerms->installments ) ;
    }

    public function testConstructorAcceptsDocumentLevelAdjustments(): void
    {
        $adjustment = new Adjustment([ Adjustment::TYPE => 'shipping' , Adjustment::REASON => 'Carriage' ]) ;

        $document = new BusinessDocument([ BusinessDocument::ADJUSTMENTS => [ $adjustment ] ]) ;

        $this->assertIsArray( $document->adjustments ) ;
        $this->assertInstanceOf( Adjustment::class , $document->adjustments[ 0 ] ) ;
        $this->assertSame( 'Carriage' , $document->adjustments[ 0 ]->reason ) ;
    }

    public function testPaymentTermsAcceptsFreeText(): void
    {
        $document = new BusinessDocument([ BusinessDocument::PAYMENT_TERMS => 'Net 30 days' ]) ;

        $this->assertSame( 'Net 30 days' , $document->paymentTerms ) ;
    }

    public function testConstructorKeepsAssignedSellerAsRead(): void
    {
        $key = new BusinessDocument([ BusinessDocument::ASSIGNED_SELLER => '147737218' ]) ;
        $this->assertSame( '147737218' , $key->assignedSeller ) ;

        $id = new BusinessDocument([ BusinessDocument::ASSIGNED_SELLER => 147737218 ]) ;
        $this->assertSame( 147737218 , $id->assignedSeller ) ;

        // the constructor assigns raw : a joined row stays an array on this path
        $joined = new BusinessDocument([ BusinessDocument::ASSIGNED_SELLER => [ 'name' => 'Paul Vendeur' ] ]) ;
        $this->assertIsArray( $joined->assignedSeller ) ;
        $this->assertSame( 'Paul Vendeur' , $joined->assignedSeller[ 'name' ] ) ;

        $person = new BusinessDocument([ BusinessDocument::ASSIGNED_SELLER => new Person([ 'name' => 'Paul Vendeur' ]) ]) ;
        $this->assertInstanceOf( Person::class , $person->assignedSeller ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionResolvesAssignedSeller(): void
    {
        $document = new Reflection()->hydrate
        (
            [ BusinessDocument::ASSIGNED_SELLER => [ 'name' => 'Paul Vendeur' , '_key' => '147737218' ] ] ,
            BusinessDocument::class
        );

        $this->assertInstanceOf( Person::class , $document->assignedSeller ) ;
        $this->assertSame( 'Paul Vendeur' , $document->assignedSeller->name ) ;
        $this->assertSame( '147737218' , $document->assignedSeller->_key ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionResolvesEveryAssignedSellerOfAList(): void
    {
        $document = new Reflection()->hydrate
        (
            [ BusinessDocument::ASSIGNED_SELLER => [ [ 'name' => 'Paul Vendeur' ] , [ 'name' => 'Marie Vendeuse' ] ] ] ,
            BusinessDocument::class
        );

        $this->assertIsArray( $document->assignedSeller ) ;
        $this->assertContainsOnlyInstancesOf( Person::class , $document->assignedSeller ) ;
        $this->assertSame( 'Marie Vendeuse' , $document->assignedSeller[ 1 ]->name ) ;
    }

    /**
     * @throws ReflectionException
     */
    public function testReflectionLeavesAnUnjoinedAssignedSellerReferenceAsRead(): void
    {
        $document = new Reflection()->hydrate
        (
            [ BusinessDocument::ASSIGNED_SELLER => '147737218' ] ,
            BusinessDocument::class
        );

        $this->assertSame( '147737218' , $document->assignedSeller ) ;
    }

    public function testAssignedSellerIsNotTheIssuingSeller(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::SELLER          => new Organization([ 'name' => 'ACME Supplies' ]) ,
            BusinessDocument::ASSIGNED_SELLER => '147737218' ,
        ]);

        $this->assertInstanceOf( Organization::class , $document->seller ) ;
        $this->assertSame( '147737218' , $document->assignedSeller ) ;
    }

    public function testConstructorKeepsPartiesAsRawArrays(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::CUSTOMER => [ 'name' => 'Jane Doe' ] ,
            BusinessDocument::SELLER   => [ 'name' => 'ACME Supplies' ] ,
            BusinessDocument::AUTHOR   => [ 'name' => 'ACME Supplies' ] ,
        ]);

        $this->assertIsArray( $document->customer ) ;
        $this->assertSame( 'Jane Doe' , $document->customer[ 'name' ] ) ;

        $this->assertIsArray( $document->seller ) ;
        $this->assertSame( 'ACME Supplies' , $document->seller[ 'name' ] ) ;

        $this->assertIsArray( $document->author ) ;
        $this->assertSame( 'ACME Supplies' , $document->author[ 'name' ] ) ;
    }

    /**
     * A weight travels as the plain number it usually is, without being cast
     * to a structure it does not need.
     */
    public function testWeightKeepsAPlainNumberAsRead(): void
    {
        $float = new BusinessDocument([ BusinessDocument::WEIGHT => 326.5456 ]) ;
        $this->assertSame( 326.5456 , $float->weight ) ;

        $int = new BusinessDocument([ BusinessDocument::WEIGHT => 42 ]) ;
        $this->assertSame( 42 , $int->weight ) ;
    }

    /**
     * The constructor assigns raw, `#[HydrateAs]` acting through
     * `Reflection::hydrate()` alone : a weight read as a row sits in the
     * property until hydration replaces it, exactly like the other properties
     * of the class.
     */
    public function testConstructorKeepsAStructuredWeightAsRawArray(): void
    {
        $document = new BusinessDocument([ BusinessDocument::WEIGHT => [ 'value' => 326.5456 , 'unitCode' => 'KGM' ] ]) ;

        $this->assertIsArray( $document->weight ) ;
        $this->assertSame( 326.5456 , $document->weight[ 'value' ] ) ;
    }

    /**
     * A weight that states its unit comes back typed, so a consumer reads the
     * unit instead of assuming one.
     * @throws ReflectionException
     */
    public function testReflectionHydratesAWeightThatStatesItsUnit(): void
    {
        $document = new Reflection()->hydrate
        (
            [ BusinessDocument::WEIGHT => [ 'value' => 326.5456 , 'unitCode' => 'KGM' ] ] ,
            BusinessDocument::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $document->weight ) ;
        $this->assertSame( 326.5456 , $document->weight->value ) ;
        $this->assertSame( 'KGM' , $document->weight->unitCode ) ;
    }

    /**
     * The volume travels beside the weight and reads exactly the same way —
     * a plain number, or a quantity stating its unit.
     *
     * @throws ReflectionException
     */
    public function testVolumeIsReadLikeTheWeight(): void
    {
        $plain = new BusinessDocument([ BusinessDocument::VOLUME => 3.412 ]) ;
        $this->assertSame( 3.412 , $plain->volume ) ;

        $stated = new Reflection()->hydrate
        (
            [ BusinessDocument::VOLUME => [ 'value' => 3.412 , 'unitCode' => 'MTQ' ] ] ,
            BusinessDocument::class
        );

        $this->assertInstanceOf( QuantitativeValue::class , $stated->volume ) ;
        $this->assertSame( 3.412 , $stated->volume->value    ) ;
        $this->assertSame( 'MTQ' , $stated->volume->unitCode ) ;
    }

    /**
     * The weight is not part of the monetary summary : both live on the
     * document, and neither reaches into the other.
     */
    public function testWeightStandsBesideTheMonetaryTotals(): void
    {
        $document = new BusinessDocument
        ([
            BusinessDocument::TOTALS => new DocumentTotals([ 'total' => [ 'value' => 100 , 'currency' => 'EUR' ] ]) ,
            BusinessDocument::WEIGHT => 326.5456 ,
        ]);

        $this->assertInstanceOf( DocumentTotals::class , $document->totals ) ;
        $this->assertSame( 326.5456 , $document->weight ) ;
        $this->assertFalse( property_exists( $document->totals , BusinessDocument::WEIGHT ) ) ;
    }
}
