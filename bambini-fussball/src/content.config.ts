import { defineCollection, z } from 'astro:content';
import { glob } from 'astro/loaders';

const articleSchema = z.object({
  title: z.string().max(60, 'Title must be 60 characters or less'),
  description: z.string().max(160, 'Description must be 160 characters or less'),
  pubDate: z.coerce.date(),
  updatedDate: z.coerce.date().optional(),
  author: z.string().default('Redaktion'),
  image: z.string().optional(),
});

const trainer = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/trainer' }),
  schema: articleSchema,
});

const eltern = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/eltern' }),
  schema: articleSchema,
});

const vereine = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/vereine' }),
  schema: articleSchema,
});

export const collections = { trainer, eltern, vereine };
