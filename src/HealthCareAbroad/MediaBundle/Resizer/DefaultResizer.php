<?php
namespace HealthCareAbroad\MediaBundle\Resizer;

use Imagine\Image\ImagineInterface;
use Imagine\Image\ImageInterface;
use Imagine\Image\Box;
use Imagine\Exception\InvalidArgumentException;
use Gaufrette\File;
use HealthCareAbroad\MediaBundle\Entity\Media;

class DefaultResizer implements Resizer
{
    /**
     *
     * @var \Imagine\Image\ImagineInterface $adapter
     */
    protected $adapter;

    protected $mode;

    /**
     * @param \Imagine\Image\ImagineInterface $adapter
     * @param string $mode
     */
    public function __construct(ImagineInterface $adapter, $mode=ImageInterface::THUMBNAIL_OUTBOUND)
    {
        $this->adapter = $adapter;
        $this->mode    = $mode;
    }

    /**
     * {@inheritdoc}
     */
    public function resize(Media $media, File $in, File $out, $format, array $settings)
    {

        if (!array_key_exists('quality', $settings)) {
            $settings['quality'] = 100;
        }

        if (!isset($settings['width'])) {
            throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getName()));
        }

        $image = $this->adapter->load($in->getContent());

        $content = $image
            ->thumbnail($this->getBox($media, $settings), $this->mode)
            ->get($format, array('quality' => $settings['quality']));

        $out->setContent($content);
    }

    /**
     * {@inheritdoc}
     */
    public function getBox(Media $media, array $settings)
    {
        $size = $media->getBox();

        if ($settings['width'] == null && $settings['height'] == null) {
            throw new \RuntimeException(sprintf('Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.', $media->getContext(), $media->getProviderName()));
        }

        if ($settings['height'] == null) {
            $settings['height'] = (int) ($settings['width'] * $size->getHeight() / $size->getWidth());
        }

        if ($settings['width'] == null) {
            $settings['width'] = (int) ($settings['height'] * $size->getWidth() / $size->getHeight());
        }

        return $this->computeBox($media, $settings);
    }

    /**
     * @throws \Imagine\Exception\InvalidArgumentException
     *
     * @param HealthCareAbroad\MediaBundle\Entity\Media $media
     * @param array $settings
     *
     * @return \Imagine\Image\Box
     */
    private function computeBox(Media $media, array $settings)
    {
        if ($this->mode !== ImageInterface::THUMBNAIL_INSET && $this->mode !== ImageInterface::THUMBNAIL_OUTBOUND) {
            throw new InvalidArgumentException('Invalid mode specified');
        }
        
        $size = new Box($settings['width'], $settings['height']);

        $ratios = array(
            $settings['width'] / $size->getWidth(),
            $settings['height'] / $size->getHeight()
        );

        if ($this->mode === ImageInterface::THUMBNAIL_INSET) {
            $ratio = min($ratios);
        } else {
            $ratio = max($ratios);
        }

        return $size->scale($ratio);
    }

    public function setModeToInset()
    {
        $this->mode = ImageInterface::THUMBNAIL_INSET;
    }
}