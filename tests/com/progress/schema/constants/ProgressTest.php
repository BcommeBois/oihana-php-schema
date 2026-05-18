<?php

namespace tests\com\progress\schema\constants ;

use com\progress\schema\constants\Progress ;
use PHPUnit\Framework\TestCase ;

class ProgressTest extends TestCase
{
    public function testSchemaConstant(): void
    {
        $this->assertSame( 'https://schema.progress.com' , Progress::SCHEMA );
    }

    public function testCommonConstants(): void
    {
        $this->assertSame( 'name'    , Progress::NAME    );
        $this->assertSame( 'owner'   , Progress::OWNER   );
        $this->assertSame( 'creator' , Progress::CREATOR );
        $this->assertSame( 'type'    , Progress::TYPE    );
        $this->assertSame( 'status'  , Progress::STATUS  );
    }

    public function testColumnConstants(): void
    {
        $this->assertSame( 'columnId'   , Progress::COLUMN_ID   );
        $this->assertSame( 'columnType' , Progress::COLUMN_TYPE );
        $this->assertSame( 'charSet'    , Progress::CHAR_SET    );
        $this->assertSame( 'collation'  , Progress::COLLATION   );
        $this->assertSame( 'width'      , Progress::WIDTH       );
    }

    public function testIndexConstants(): void
    {
        $this->assertSame( 'indexType'     , Progress::INDEX_TYPE     );
        $this->assertSame( 'indexSequence' , Progress::INDEX_SEQUENCE );
        $this->assertSame( 'unique'        , Progress::UNIQUE         );
        $this->assertSame( 'abbreviate'    , Progress::ABBREVIATE     );
    }

    public function testUserConstants(): void
    {
        $this->assertSame( 'grantee'        , Progress::GRANTEE         );
        $this->assertSame( 'grantor'        , Progress::GRANTOR         );
        $this->assertSame( 'dbaAccess'      , Progress::DBA_ACCESS      );
        $this->assertSame( 'resourceAccess' , Progress::RESOURCE_ACCESS );
    }

    public function testAuthorizationConstants(): void
    {
        $this->assertSame( 'select'     , Progress::SELECT     );
        $this->assertSame( 'insert'     , Progress::INSERT     );
        $this->assertSame( 'update'     , Progress::UPDATE     );
        $this->assertSame( 'delete'     , Progress::DELETE     );
        $this->assertSame( 'references' , Progress::REFERENCES );
        $this->assertSame( 'alter'      , Progress::ALTER      );
        $this->assertSame( 'index'      , Progress::INDEX      );
    }

    public function testConstraintConstants(): void
    {
        $this->assertSame( 'constraintName' , Progress::CONSTRAINT_NAME );
        $this->assertSame( 'constraintType' , Progress::CONSTRAINT_TYPE );
        $this->assertSame( 'updateRule'     , Progress::UPDATE_RULE     );
        $this->assertSame( 'deleteRule'     , Progress::DELETE_RULE     );
    }
}
