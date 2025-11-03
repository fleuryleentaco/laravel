<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\TextAnalysis;

class TextAnalysisTest extends TestCase
{
    use TextAnalysis;

    /**
     * Test word counting with Unicode characters
     */
    public function test_count_words_with_unicode(): void
    {
        $text = "Ceci est un test avec des accents éàù";
        $count = $this->countWords($text);
        $this->assertEquals(8, $count);
    }

    /**
     * Test word counting with empty text
     */
    public function test_count_words_empty_text(): void
    {
        $count = $this->countWords('');
        $this->assertEquals(0, $count);
    }

    /**
     * Test word counting with HTML tags
     */
    public function test_count_words_with_html(): void
    {
        $text = "<p>Ceci est un <strong>test</strong></p>";
        $count = $this->countWords($text);
        $this->assertEquals(4, $count);
    }

    /**
     * Test shingles generation
     */
    public function test_shingles_generation(): void
    {
        $text = "ceci est un test simple";
        $shingles = $this->shinglesText($text, 3);
        
        $this->assertIsArray($shingles);
        $this->assertArrayHasKey('ceci est un', $shingles);
        $this->assertArrayHasKey('est un test', $shingles);
        $this->assertArrayHasKey('un test simple', $shingles);
    }

    /**
     * Test MinHash computation
     */
    public function test_minhash_computation(): void
    {
        $text = "Ceci est un document de test pour vérifier le calcul de MinHash";
        $minhash = $this->computeMinHash($text, 5, 64);
        
        $this->assertIsArray($minhash);
        $this->assertCount(64, $minhash);
        
        // All values should be integers
        foreach ($minhash as $hash) {
            $this->assertIsInt($hash);
        }
    }

    /**
     * Test MinHash similarity calculation
     */
    public function test_minhash_similarity(): void
    {
        $text1 = "Ceci est un document de test";
        $text2 = "Ceci est un document de test";
        $text3 = "Complètement différent";
        
        $hash1 = $this->computeMinHash($text1, 5, 64);
        $hash2 = $this->computeMinHash($text2, 5, 64);
        $hash3 = $this->computeMinHash($text3, 5, 64);
        
        // Identical texts should have similarity = 1
        $sim12 = $this->minhashSimilarity($hash1, $hash2);
        $this->assertEquals(1.0, $sim12);
        
        // Different texts should have low similarity
        $sim13 = $this->minhashSimilarity($hash1, $hash3);
        $this->assertLessThan(0.3, $sim13);
    }

    /**
     * Test Jaccard similarity calculation
     */
    public function test_jaccard_similarity(): void
    {
        $text1 = "Ceci est un document de test";
        $text2 = "Ceci est un document de test";
        $text3 = "Ceci est différent";
        
        // Identical texts
        $sim12 = $this->jaccardSimilarityText($text1, $text2, 5);
        $this->assertEquals(1.0, $sim12);
        
        // Partially similar texts
        $sim13 = $this->jaccardSimilarityText($text1, $text3, 5);
        $this->assertGreaterThan(0, $sim13);
        $this->assertLessThan(1.0, $sim13);
    }

    /**
     * Test basic rules checking - too short
     */
    public function test_basic_rules_too_short(): void
    {
        $shortText = "Trop court";
        $errors = $this->checkBasicRules($shortText);
        
        $this->assertNotEmpty($errors);
        $this->assertEquals('too_short', $errors[0]['type']);
    }

    /**
     * Test basic rules checking - valid text
     */
    public function test_basic_rules_valid_text(): void
    {
        $validText = "Ceci est un texte valide avec suffisamment de mots pour passer toutes les vérifications de base sans aucun contenu interdit";
        $errors = $this->checkBasicRules($validText);
        
        $this->assertEmpty($errors);
    }
}
